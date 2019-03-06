<?php

declare(strict_types=1);

namespace Devayes\Sinput;

use Illuminate\Http\Request;
use Illuminate\Contracts\Config\Repository;

class Sinput
{

    /**
     * @var $decode_input
     */
    public static $decode_input = true;

    /**
     * @var $decode_output
     */
    public static $decode_output = true;

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
        self::$decode_input = $config->get('sinput.decode_input') ?? true;
        self::$decode_output = $config->get('sinput.decode_output') ?? true;
    }

    /**
     * @param boolean    $decode
     *
     * @return void
     */
    public static function setDecodeInput($decode = true)
    {
        self::$decode_input = (bool)$decode;
    }

    /**
     * @param boolean    $decode
     *
     * @return void
     */
    public static function setDecodeOutput($decode = true)
    {
        self::$decode_output = (bool)$decode;
    }

    /**
     * @param mixed $config
     *
     * @return array
     */
    public function all($config = null)
    {
        $values = $this->request->all();

        return $this->clean($values, $config);
    }

    /**
     * @param string $key
     * @param mixed  $default
     * @param mixed   $config
     *
     * @return mixed
     */
    public function get(string $key, $default = null, $config = null)
    {
        $value = $this->request->input($key, $default);

        return $this->clean($value, $config);
    }

    /**
     * @param string|string[] $keys
     * @param mixed $config
     *
     * @return array
     */
    public function only($keys, $config = null)
    {
        $values = [];
        foreach ((array)$keys as $key) {
            $values[$key] = $this->get($key, null, $config);
        }

        return $values;
    }

    /**
     * @param string|string[] $keys
     * @param mixed $config
     *
     * @return array
     */
    public function except($keys, $config = null)
    {
        $values = $this->request->except((array) $keys);

        return $this->clean($values, $config);
    }

    /**
     * @param string $key
     * @param mixed $default
     * @param mixed $config
     *
     * @return mixed
     */
    public function old(string $key, $default = null, $config = null)
    {
        $value = $this->request->old($key, $default);

        return $this->clean($value, $config);
    }

    /**
     * @param string $keys
     * @param mixed $config
     *
     * @return mixed
     */
    public function list(array $keys, $config = null)
    {
        if (is_array($keys)) {
            $return = [];
            foreach ($keys as $index) {
                array_push($return, $this->get($index, null, $config));
            }
            return $return;
        } elseif (is_string($keys)) {
            return $this->get($keys, null, $config);
        }

        return null;
    }

    /**
     * Ex: Sinput::match("#^perm_#"); matches: perm_thing, perm_stuff
     * @param  string     $regex
     * @param  mixed    $config
     * @return array
     */
    public static function match($regex, $config = null)
    {
        $return = [];
        foreach ($this->request->all() as $key => $value) {
            if (preg_match($regex, $key)) {
                $return[$key] = $this->clean($value, $config);
            }
        }

        return $return;
    }

    /**
     * @param mixed $value
     * @param mixed $config
     *
     * @return mixed
     */
    protected function clean($value, $config = null)
    {
        if (empty($value) || is_bool($value) || is_int($value) || is_float($value)) {
            return $value;
        }

        if (is_array($value)) {
            return array_map(function ($item) use ($config) {
                return $this->clean($item, $config);
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
        if (self::$decode_input) {
            $value = $this->decode($value);
        }

        $value = app('purifier')->clean($value, $config);

        if (self::$decode_output) {
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
