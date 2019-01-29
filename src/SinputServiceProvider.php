<?php

declare(strict_types=1);

namespace Devayes\Sinput;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;

class SinputServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register()
    {
        $this->app->singleton('sinput', function (Container $app) {
            return new Sinput($app['request']);
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
