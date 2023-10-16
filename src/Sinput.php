<?php

declare(strict_types=1);

namespace Devayes\Sinput;

use HTMLPurifier;
use HTMLPurifier_Config;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Contracts\Config\Repository;

class Sinput
{

    /**
     * Macro support
     */
    use Macroable;

    /**
     * @var $config
     */
    protected $config;

    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * @var HTMLPurifier
     */
    protected $purifier;

    /**
     * @var Ruleset
     */
    protected $ruleset = null;


    /**
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Contracts\Config\Repository $config
     * @param \Illuminate\Filesystem\Filesystem $files
     *
     * @return void
     */
    public function __construct(Request $request, Repository $config, Filesystem $files)
    {
        $this->config = $config->get('sinput');
        $this->request = $request;

        $config_dir = Arr::get($this->config, 'purifier.cache_path');
        if ($config_dir && !$files->isDirectory($config_dir)) {
            $files->makeDirectory($config_dir, Arr::get($this->config, 'purifier.cache_file_mode', 0755), true);
        }

        $this->purifier = new HTMLPurifier($this->getPurifierConfig());
    }

    /**
     * Used for sinput helper method.
     *
     * @param string|null $index
     * @param mixed $default
     * @param string|null $ruleset
     * @return null|string
     */
    public function input(?string $index = null, $default = null, ?string $ruleset = null)
    {
        return $this->clean($this->request->input($index, $default), $ruleset);
    }

    /**
     * Get HTMLPurifier Config
     * @date   2019-06-04
     * @param  mixed     $ruleset
     * @return HTMLPurifier_Config
     */
    protected function getPurifierConfig($ruleset = null)
    {
        $config = HTMLPurifier_Config::createDefault();

        if (!Arr::get($this->config, 'purifier.finalize')) {
            $config->autoFinalize = false;
        }

        $opts = [
            'Core.Encoding' => Arr::get($this->config, 'purifier.encoding', 'UTF-8'),
            'Cache.SerializerPath' => Arr::get($this->config, 'purifier.cache_path', storage_path('app/purifier')),
            'Cache.SerializerPermissions' => Arr::get($this->config, 'purifier.cache_file_mode', 0755)
        ];

        if (!$ruleset) {
            $default = Arr::get($this->config, 'default_ruleset', 'default');
            if (empty($default)) {
                throw new \Exception('Sinput default ruleset "' . $default . '" does not exist.');
            }
            $opts = array_merge($opts, Arr::get($this->config, 'purifier.rulesets.' . $default, []));
        } elseif (is_string($ruleset)) {
            $rules = Arr::get($this->config, 'purifier.rulesets.' . $ruleset, []);
            if (empty($rules)) {
                throw new \Exception('Sinput ruleset "' . $ruleset . '" does not exist.');
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
     * @param mixed   $opt
     * @param mixed   $value
     *
     * @return void
     */
    public function setConfig($opt, $value)
    {
        Arr::set($this->config, $opt, $value);
    }

    /**
     * Set a ruleset
     *
     * @param string|null $ruleset
     * @return Sinput
     */
    public function setRuleSet(?string $ruleset = null)
    {
        $this->ruleset = $ruleset;
        return $this;
    }

    /**
     * @param mixed $value
     * @param string|null $ruleset
     * @param mixed $default
     *
     * @return mixed
     */
    public function clean($value, $ruleset = null, $default = null)
    {
        if (
            is_numeric($value)
            || is_int($value)
            || is_float($value)
            || is_bool($value)
            || is_null($value)
            || is_object($value)
            || is_resource($value)
        ) {
            return $value;
        } elseif (empty($value)) {
            $value = $default;
        }

        if (empty($value)) {
            return $value;
        }

        if (empty($ruleset) && $this->ruleset) {
            $ruleset = $this->ruleset;
        }

        if (is_array($value)) {
            return array_map(function ($item) use ($default, $ruleset) {
                return $this->clean($item, $ruleset, $default);
            }, $value);
        }

        return $this->purify($value, $ruleset);
    }

    /**
     * @param string $value
     * @param mixed $config
     *
     * @return mixed
     */
    protected function purify($value, $config = null)
    {

        if (Arr::get($this->config, 'decode_input')) {
            $value = self::decode($value);
        }

        $config = $config ?? Arr::get($this->config, 'default_ruleset');

        $value = $this->purifier->purify(
            $value,
            ($config ? $this->getPurifierConfig($config) : null)
        );

        if (Arr::get($this->config, 'decode_output')) {
            $value = self::decode($value);
        }

        return $value;
    }

    /**
     * @param array|string $value
     *
     * @return array|string
     */
    protected static function decode($value)
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
}
