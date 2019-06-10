<?php

declare(strict_types=1);

namespace Devayes\Sinput;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Application as LaravelApplication;
use Laravel\Lumen\Application as LumenApplication;

class SinputServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return null
     */
    public function boot()
    {
        if ($this->app instanceof LaravelApplication) {
            $this->publishes([$this->getConfigSource() => config_path('sinput.php')]);
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('sinput');
        }
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
     * @return string[]
     */
    public function provides()
    {
        return ['sinput'];
    }
}
