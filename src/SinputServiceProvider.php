<?php

declare(strict_types=1);

/*
 * This file is part of Laravel Sinput.
 *
 * (c) Devin Hayes <devayes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace devayes\Sinput;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;

/**
 * This is the sinput service provider class.
 *
 * @author Devin Hayes <devayes@gmail.com>
 */
class SinputServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
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
     * Get the services provided by the provider.
     *
     * @return string[]
     */
    public function provides()
    {
        return ['sinput'];
    }
}
