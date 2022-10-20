<?php

namespace Plugide\Define\Providers;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Plugide\Define\Contracts\PluginProvider;
use Plugide\Define\Plug;

class DefineServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Plug::start();
        $this->registerPlugins();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->guessFactory();
        $this->registerAssets();
    }

    /**
     * Register services providers and extend by the plugin's.
     *
     * @return void
     */
    public function registerPlugins()
    {
        foreach (Plug::plugins() as $plugin) {
            foreach ($plugin->package['providers'] ?? [] as $provider) {
                $instance = (new $provider($this->app));

                if ($instance instanceof PluginProvider) {
                    $instance->plugin($plugin);
                    $this->app->register($instance);
                } else {
                    $this->app->register($provider);
                }
            }

            if (method_exists($plugin, 'extend')) {
                foreach ($plugin->extend() as $extend) {
                    $extend->extend();
                }
            }
        }
    }

    /**
     * Register Assets Plugin´s.
     *
     * @return void
     */
    public function registerAssets()
    {
        if (Plug::data('assets.enable')) {
            Route::middleware('web')
                ->get(Plug::data('assets.route'), Plug::data('assets.class'))
                ->where('patch', '.*')
                ->name(Plug::data('assets.name'))
                ->domain(Plug::data('assets.domain'));
        }
    }

    /**
     * Configure Factory Names.
     *
     * @return void
     */
    public function guessFactory()
    {
        Factory::guessFactoryNamesUsing(function (string $modelName) {
            $modelName = Str::startsWith($modelName, 'App\\Models\\')
                ? Str::after($modelName, 'App\\Models\\')
                : Str::after($modelName, 'App\\');

            if (Str::contains($modelName, '\\Models')) {
                $moduleName = Str::before($modelName, '\\Models');
                $model = Str::after($modelName, 'Models\\');
                $modelName = $moduleName.'\\'.$model;
            }

            return 'Database\\Factories\\'.$modelName.'Factory';
        });
    }
}
