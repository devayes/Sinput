<?php

declare(strict_types=1);

namespace Devayes\Sinput;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
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
        $this->loadRequestMacros();
    }

    /**
     * Load request macros
     *
     * @return void
     */
    protected function loadRequestMacros()
    {
        /**
         * eg: request()->scrub()->all() // Remove html from all inputs. Applies `default_ruleset` option.
         * eg: request()->scrub(['foo','bar'], 'allow_html')->all() // Allow html for 'foo' and 'bar' inputs by applying the `allow_html` ruleset option.
         * eg: request()->scrub(['title', 'subtitle'], 'titles')->all() // Apply a custom ruleset config `titles` to 'title' and 'subtitle' inputs.
         * eg: request()->scrub('foo', 'allow_html')->scrub('bar', 'no_html')->only(['foo', 'bar']); // Allow html in 'foo' input, strip html from 'bar' input.
         */
        Request::macro('scrub', function ($fields = null, ?string $ruleset = null): \Illuminate\Http\Request {
            if (empty($fields)) {
                $keys = array_keys($this->allFiles());
                if (!empty($keys)) {
                    $this->merge(scrub((array)$this->except($keys), $ruleset));
                }
            } elseif ($data = $this->only((array)$fields)) {
                $this->merge(scrub($data, $ruleset));
            }
            return $this;
        });
        // $foo = $request->sinput('foo', 'default value', $ruleset);
        Request::macro('sinput', function ($field, $default = null, ?string $ruleset = null) {
            return sinput($field, $default, $ruleset);
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
