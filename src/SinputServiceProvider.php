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
     * @return null
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
     * LOad the blade directive.
     * @date   2019-06-10
     * @return null
     */
    protected function loadBladeDirective()
    {
        Blade::directive('sinput', function($expression) {
            $parts = explode(',', $expression);
            $var = (empty($parts['0']) ? null : $parts['0']);
            if (isset($parts['1'])) {
                $config = $parts['1'];
                return "<?php echo sinput()->clean($var, null, $config); ?>";
            }
            return "<?php echo sinput()->clean($var); ?>";
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
     * @return string[]
     */
    public function provides()
    {
        return ['sinput'];
    }
}