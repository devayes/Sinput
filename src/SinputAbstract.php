<?php

declare(strict_types=1);

namespace Devayes\Sinput;

use HTMLPurifier;
use HTMLPurifier_Config;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Contracts\Config\Repository;

abstract class SinputAbstract
{

    /**
     * @var $config
     */
    protected $config;

    /**
     * @var $method
     */
    protected $method = 'input';

    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * @var HTMLPurifier
     */
    protected $purifier;


    /**
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Contracts\Config\Repository $config
     * @param \Illuminate\Filesystem\Filesystem $files
     *
     * @return void
     */
    public function __construct(Request $request, Repository $config, Filesystem $files)
    {
        $this->request = $request;
        $this->config = $config->get('sinput');

        $config_dir = Arr::get($this->config, 'purifier.cache_path');
        if ($config_dir && ! $files->isDirectory($config_dir)) {
            $files->makeDirectory($config_dir, Arr::get($this->config, 'purifier.cache_file_mode', 0755), true);
        }

        $this->purifier = new HTMLPurifier($this->getPurifierConfig());
    }

    /**
     * Get HTMLPurifier Config
     * @date   2019-06-04
     * @param  mixed     $ruleset
     * @return object
     */
    protected function getPurifierConfig($ruleset = null)
    {
        $config = HTMLPurifier_Config::createDefault();

        if (! Arr::get($this->config, 'purifier.finalize')) {
            $config->autoFinalize = false;
        }

        $opts = [
            'Core.Encoding' => Arr::get($this->config, 'purifier.encoding', 'UTF-8'),
            'Cache.SerializerPath' => Arr::get($this->config, 'purifier.cache_path', storage_path('app/purifier')),
            'Cache.SerializerPermissions' => Arr::get($this->config, 'purifier.cache_file_mode', 0755)
        ];

        if (! $ruleset) {
            $default = Arr::get($this->config, 'default_ruleset', 'default');
            if (empty($default)) {
                throw new SinputException('Sinput default ruleset "'.$default.'" does not exist.');
            }
            $opts = array_merge($opts, Arr::get($this->config, 'purifier.rulesets.'.$default, []));
        } elseif (is_string($ruleset)) {
            $rules = Arr::get($this->config, 'purifier.rulesets.'.$ruleset, []);
            if (empty($rules)) {
                throw new SinputException('Sinput ruleset "'.$ruleset.'" does not exist.');
            }
            $opts = array_merge($opts, $rules);
        } elseif (is_array($ruleset)) {
            $opts = array_merge($opts, $ruleset);
        }

        $config->loadArray($opts);

        return $config;
    }

    /**
     * @param mixed    $opt
     *
     * @return mixed
     */
    public function getConfig($opt = null)
    {
        return Arr::get($this->config, $opt, $this->config);
    }

    /**
     * @param mixed    $opt
     * @param string   $value
     *
     * @return void
     */
    public function setConfig($opt, $value)
    {
        Arr::set($this->config, $opt, $value);
    }

    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string   $method
     *
     * @return void
     */
    public function setMethod(string $method)
    {
        if (in_array($method, ['input', 'query', 'post'])) {
            $this->method = $method;
        }
    }

    /**
     * @param string $method
     *
     * @return boolean
     */
    public function isMethod(string $method): bool
    {
        return $this->request->isMethod($method);
    }

    /**
     * @param mixed $key
     * @return boolean
     */
    public function has($key): bool
    {
        return $this->request->has($key);
    }

    /**
     * @param mixed $key
     * @return boolean
     */
    public function hasAny($keys): bool
    {
        return $this->request->hasAny($keys);
    }

    /**
     * @param mixed $config
     *
     * @return array
     */
    public function all($config = null): array
    {
        return $this->clean(
            $this->request->all(),
            null,
            $config
        );
    }

    /**
     * @param string $key
     * @param mixed  $default
     * @param mixed  $config
     *
     * @return mixed
     */
    public function input(string $key, $default = null, $config = null)
    {
        return $this->clean(
            $this->request->input($key, $default),
            null,
            $config
        );
    }

    /**
     * @param string $key
     * @param mixed  $default
     * @param mixed  $config
     *
     * @return mixed
     */
    public function query(string $key, $default = null, $config = null)
    {
        return $this->clean(
            $this->request->query($key, $default),
            null,
            $config
        );
    }

    /**
     * @param string $key
     * @param mixed  $default
     * @param mixed  $config
     *
     * @return mixed
     */
    public function post(string $key, $default = null, $config = null)
    {
        return $this->clean(
            $this->request->post($key, $default),
            $default,
            $config
        );
    }

    /**
     * @param string|string[] $keys
     * @param mixed $config
     *
     * @return array
     */
    public function only($keys, $config = null): array
    {
        return $this->clean(
            $this->request->only($keys),
            null,
            $config
        );
    }

    /**
     * @param string|string[] $keys
     * @param mixed $config
     *
     * @return array
     */
    public function except($keys, $config = null): array
    {
        return $this->clean(
            $this->request->except($keys),
            null,
            $config
        );
    }

    /**
     * @param mixed $value
     * @param mixed $default
     * @param mixed $config
     *
     * @return mixed
     */
    public function clean($value, $default = null, $config = null)
    {
        if (is_numeric($value) || is_bool($value) || is_int($value) || is_float($value) || is_null($value)) {
            return $value;
        } elseif (empty($value) && ! is_null($default)) {
            $value = $default;
        }

        if (empty($value)) {
            return $value;
        }

        if (is_array($value)) {
            return array_map(function($item) use ($default, $config) {
                return $this->clean($item, $default, $config);
            }, $value);
        }

        return $this->purify($value, $config);
    }

    /**
     * @param string $value
     * @param mixed $config
     *
     * @return string
     */
    protected function purify(string $value, $config = null)
    {

        if (Arr::get($this->config, 'decode_input')) {
            $value = $this->decode($value);
        }

        $config = $config ?? Arr::get($this->config, 'default_ruleset');

        $value = $this->purifier->purify(
            $value,
            ($config ? $this->getPurifierConfig($config) : null)
        );

        if (Arr::get($this->config, 'decode_output')) {
            $value = $this->decode($value);
        }

        return $value;
    }

    /**
     * @param string $value
     *
     * @return string
     */
    protected function decode($value)
    {
        if (is_string($value)) {
            $value = html_entity_decode($value, ENT_QUOTES, 'UTF-8');
        } elseif (is_array($value)) {
            array_walk_recursive($value, function (&$value) {
                $value = html_entity_decode($value, ENT_QUOTES, 'UTF-8');
            });
        }

        return $value;
    }

    /**
     * Make sure string is truly empty
     *
     * @param string $var
     * @return boolean
     */
    private function isEmpty($var): bool
    {
        return (empty($var) && strlen($var) == 0);
    }
}
