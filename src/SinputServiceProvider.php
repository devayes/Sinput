<?php

declare(strict_types=1);

namespace Devayes\Sinput;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Foundation\Application as LaravelApplication;
use Laravel\Lumen\Application as LumenApplication;

class SinputServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app instanceof LaravelApplication) {
            $this->publishes([$this->getConfigSource() => config_path('sinput.php')]);
            $this->loadBladeDirective();
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('sinput');
        }
    }

    /**
     * Load request macro
     * eg: request()->scrub()->all() // No html allowed. Applies `default_ruleset` config option.
     * eg: request()->scrub('html')->all() // Allow html by applying the `html` config option.
     * eg: request()->scrub('titles')->all() // Apply a custom config setting `titles`.
     *
     * @return void
     */
    protected function loadBladeDirective()
    {
        Request::macro('scrub', function ($config = null) {
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
        return realpath(__DIR__.'/../config/sinput.php');
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
}