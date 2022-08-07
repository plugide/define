<?php

namespace Plugide\Define;

use Composer\Autoload\ClassLoader;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

class Plug
{
    /**
     * The data associated with the root plug.
     *
     * @var array|null
     */
    protected static ?array $data = null;

    /**
     * The root path.
     *
     * @var string|null
     */
    protected static ?string $path = null;

    /**
     * The plugin class.
     *
     * @var string|null
     */
    protected static ?string $plugin = null;

    /**
     * All of the registered plugins.
     *
     * @var array|null
     */
    protected static ?array $plugins = null;

    /**
     * The type class.
     *
     * @var string|null
     */
    protected static ?string $type = null;

    /**
     * All of the registered types.
     *
     * @var array|null
     */
    protected static ?array $types = null;

    /**
     * All of the registered stubs.
     *
     * @var array
     */
    public static array $stubs = [];

    /**
     * Start plug and register all plug-ins.
     *
     * @param string|null $path
     * @return self
     */
    public static function start(string $path = null): self
    {
        if (is_null(self::$path)) {
            self::root($path ?? base_path());

            self::$plugin = self::data('config.class.plugin') ?? Plugin::class;
            self::$type = self::data('config.class.type') ?? Type::class;

            (new Collection(self::data('config.types')))
                ->map(function ($id) {
                    static::$types[$id] = self::newType($id, self::data('custom.types.'.$id) ?? []);
                });

            if (file_exists(self::folder())) {
                self::finder(
                    self::folder(),
                    self::data("config.finder.filename"),
                    self::data("config.finder.exclude")
                );
            }
        }

        return new static();
    }

    /**
     * Root plug.
     *
     * @param string|null $path
     * @return void
     */
    public static function root(string $path = null): void
    {
        self::$path = $path;
        self::data();
    }

    /**
     * Data of plug.
     *
     * @return mixed
     */
    public static function data(string $key = null)
    {
        if (is_null(self::$data)) {
            $file = self::path('.plug.yml');

            if (! is_writable($file)) {
                copy(realpath(__DIR__.'/../stubs/.plug.stub'), $file);
            }

            self::$data = Yaml::parseFile($file);
        }

        if ($key) {
            return Arr::get(self::$data, $key);
        }

        return self::$data;
    }

    /**
     * Get the path of the discovery setup.
     *
     * @param string|null $path Optionally, a path to append to the base path
     * @return string
     */
    public static function path(string $path = null): string
    {
        return self::$path.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Get the folder of plugins.
     *
     * @param string|null $path Optionally, a path to append to the base path
     * @return string
     */
    public static function folder(string $path = null): string
    {
        return self::path(
            self::data('config.folder')
                ? self::data('config.folder').DIRECTORY_SEPARATOR.$path
                : $path
        );
    }

    /**
     * Finds plugins.
     *
     * @param string $folder
     * @param array $filename
     * @param array $exclude
     * @return void
     */
    public static function finder(string $folder, array $filename, array $exclude)
    {
        $finder = Finder::create()->ignoreDotFiles(false)
            ->files()->exclude($exclude)
            ->in($folder);

        foreach ($filename as $name) {
            $finder->name($name);
        }

        foreach ($finder as $file) {
            self::loader($file);
        }

        foreach ($finder as $file) {
            self::$plugins[] = self::newPlugin($file);
        }
    }

    /**
     * Plugin ClassLoader.
     *
     * @param $file
     * @return array
     */
    public static function loader($file): array
    {
        $data = Yaml::parseFile($file->getPathname());

        self::autoload(Arr::get($data, 'package.autoload') ?? [], $file->getPath());
        self::autoload(Arr::get($data, 'package.autoload-dev') ??  [], $file->getPath());

        return $data;
    }

    /**
     * Autoload.
     *
     * @param array $data
     * @param string $path
     * @return void
     */
    public static function autoload(array $data, string $path)
    {
        $loader = resolve(ClassLoader::class);
        $loader->register(true);

        foreach (Arr::get($data, 'psr-4') ?? [] as $class => $src) {
            if (is_array($src)) {
                foreach ($src as $path) {
                    if (! array_key_exists($class, $loader->getClassMap())) {
                        $loader->addPsr4($class, $path.'/'.$path);
                    }
                }
            } else {
                $loader->addPsr4($class, $path.'/'.$src);
            }
        }

        foreach (Arr::get($data, 'files') ?? [] as $require) {
            require $path.'/'.$require;
        }
    }

    /**
     * Type structure and define a plugin.
     *
     * @param $file
     * @return mixed
     */
    public static function newPlugin($file)
    {
        $define = $file->getPath().'/define.php';
        $observer = $file->getPath().'/observer.php';

        $plugin = file_exists($define) ? require $define : new self::$plugin();

        $data = Yaml::parseFile($file->getPathname());
        if ($data['core']) {
            $data['active'] = $data['core'];
        }

        return $plugin->newInstance($data, true)
            ->common([
                'file' => $file,
                'observer' => file_exists($observer) ? require $observer : null,
            ]);
    }

    /**
     * Get all plugins.
     *
     * @param null $type
     * @return array|null
     */
    public static function plugins($type = null)
    {
        if ($type && isset(static::$types[$type])) {
            return (new Collection(static::$plugins))->where('type', $type)->all();
        }

        return static::$plugins ?? [];
    }

    /**
     * Type structure and define a type.
     *
     * @param null $id
     * @param array $data
     * @return mixed
     */
    public static function newType($id = null, $data = [])
    {
        $id = Str::of($id);
        $folder = $data['folder'] ?? $id->lower()->plural();

        return new self::$type(
            array_merge([
                'id' => (string) $id->lower(),
                'name' => (string) $id->studly(),
                'plugin' => self::$plugin,
                'namespace' =>  (string) $id->studly()->plural(),
                'folder' => $folder,
                'path' => self::folder($folder),
                'stub' => 'plugin',
            ], $data)
        );
    }

    /**
     * Find the with the given key.
     *
     * @param string|null $key
     * @return \Plugide\Define\Contracts\Typable
     */
    public static function findType(?string $key)
    {
        return static::$types[$key] ?? null;
    }

    /**
     * Get all types.
     *
     * @return mixed
     */
    public static function types()
    {
        return static::$types;
    }

    /**
     * Add stub folder.
     *
     * @param $folder
     * @return mixed
     */
    public static function addStub($folder)
    {
        $data = Yaml::parseFile($folder.'/.stub.yml');
        $data['folder'] = $folder;
        self::$stubs[$data['name']] = $data;
    }

    /**
     * Get all stubs.
     *
     * @return mixed
     */
    public static function stubs()
    {
        return static::$stubs;
    }
}
