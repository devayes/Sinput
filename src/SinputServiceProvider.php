<?php

declare(strict_types=1);

namespace Devayes\Sinput;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;

class SinputServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([$this->getConfigSource() => config_path('sinput.php')]);
        $this->loadRequestMacro();
    }

    /**
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom($this->getConfigSource(), 'sinput');

        $this->app->singleton('sinput', function (Container $app) {
            return new Sinput($app['request'], $app['config'], $app['files']);
        });

        $this->app->alias('sinput', Sinput::class);
    }

    /**
     * @return array
     */
    public function provides()
    {
        return ['sinput'];
    }

    /**
     * Load request macro
     * eg: request()->sinput()->all() // No html allowed. Applies `default_ruleset` config option.
     * eg: request()->sinput('html')->all() // Allow html by applying the `html` config option.
     * eg: request()->sinput('titles')->all() // Apply a custom config setting `titles`.
     *
     * @return void
     */
    protected function loadRequestMacro()
    {
        Request::macro('sinput', function ($config = null) {
            $this->merge(scrub($this->except(array_keys($this->allFiles())), $config));
            return $this;
        });
    }

    /**
     * Get the config source.
     *
     * @return string
     */
    protected function getConfigSource()
    {
        return realpath(__DIR__ . '/config/sinput.php');
    }
}
