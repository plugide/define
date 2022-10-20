<?php

namespace Plugide\Define;

use Plugide\Define\Contracts\Plugable;
use Plugide\Define\Support\Concerns;
use Symfony\Component\Yaml\Yaml;

/**
 * @property bool exists
 * @property string type
 * @property string public
 */
class Plugin extends Prototype implements Plugable
{
    use Concerns\HasCommon;
    use Concerns\HasEvents;

    /**
     * Collect the plugins
     *
     * @return array|null
     */
    public function collect()
    {
        return Plug::plugins($this->type);
    }

    /**
     * Find file for plugin asset.
     *
     * @param string $path
     * @return string
     */
    public function assets(string $path)
    {
        return route(Plug::data('assets.name'), [$this->get(Plug::data('assets.key') ?? 'handle'), $path]);
    }

    /**
     * Get the namespace
     *
     * @param string|null $space
     * @return mixed|string
     */
    public function namespace(string $space = null)
    {
        $namespace = $this->get('namespace');

        if ($space && $namespace) {
            $namespace = $namespace."\\".$space;
        }

        return $namespace;
    }

    /**
     * Path plugin.
     *
     * @param string|null $filename
     * @return null|string
     */
    public function path(string $filename = null)
    {
        $path = null;

        if ($this->exists) {
            $path = dirname($this->common('file')->getPathname());
        }

        if ($filename && $path) {
            $path = $path.'/'.$filename;
        }

        return $path;
    }

    /**
     * Find file public.
     *
     * @param string|null $path
     * @return string
     */
    public function public(string $path = null): string
    {
        return $this->path($this->public).($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Type associated with the plugin's.
     *
     * @param null $type
     * @return \Plugide\Define\Contracts\Typable|static|null
     */
    public function type($type = null)
    {
        if (is_null($type)) {
            return Plug::types($this->type);
        }

        $this->type = $type;

        return $this;
    }

    /**
     * Write file yml.
     *
     * @return false|int
     */
    public function write()
    {
        $yaml = Yaml::dump($this->getAttributes(), 100, 2, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK);

        return file_put_contents($this->common('file')->getPathname(), $yaml);
    }
}
