<?php

declare(strict_types=1);

/*
 * Laravel Sinput.
 */

namespace devayes\Sinput;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;

/**
 * @author Devin Hayes <devayes@gmail.com>
 */
class SinputServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register()
    {
        $this->app->singleton('sinput', function (Container $app) {
            $request = $app['request'];
            return new Sinput($request);
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
