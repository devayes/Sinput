<?php

declare(strict_types=1);

namespace Devayes\Sinput;

use Illuminate\Http\Request;
use Illuminate\Contracts\Config\Repository;

abstract class SinputAbstract
{

    /**
     * @var $config
     */
    protected $config;

    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return void
     */
    public function __construct(Request $request, Repository $config)
    {
        $this->request = $request;
        $this->config = $config->get('sinput');
    }

    /**
     * @param mixed    $opt
     *
     * @return mixed
     */
    public function getConfig($opt = null)
    {
        if (isset($this->config[$opt])) {
            return $this->config[$opt];
        }

        return $this->config;
    }

    /**
     * @param mixed    $opt
     * @param string   $value
     *
     * @return null
     */
    public function setConfig($opt, $value)
    {
        if (isset($this->config[$opt])) {
            $this->config[$opt] = $value;
        }
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
        if (is_bool($value) || is_int($value) || is_float($value)) {
            return $value;
        } elseif (empty($value) && ! is_null($default)) {
            $value = $default;
        }

        if (empty($value)) {
            return $value;
        }

        if (is_array($value)) {
            return array_map(function ($item) use ($default, $config) {
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
        if ($this->config['decode_input']) {
            $value = $this->decode($value);
        }

        $config = $config ?? $this->config['default_ruleset'];

        $value = app('purifier')->clean($value, $config);

        if ($this->config['decode_output']) {
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
}
