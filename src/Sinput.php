<?php

declare(strict_types=1);

namespace devayes\Sinput;

use Illuminate\Http\Request;

class Sinput
{
    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
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
     * Alias of the get method.
     *
     * @param string $key
     * @param mixed $default
     * @param mixed $config
     *
     * @return mixed
     */
    public function input(string $key, $default = null, $config = null)
    {
        return $this->get($key, $default, $config);
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
        foreach ((array) $keys as $key) {
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
     * @param string[] $keys
     * @param mixed $config
     *
     * @return array
     */
    public function map(array $keys, $config = null)
    {
        $values = $this->only(array_keys($keys), $config);

        $new = [];
        foreach ($keys as $key => $value) {
            $new[$value] = array_get($values, $key);
        }

        return $new;
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
     * @param mixed $value
     * @param mixed $config
     *
     * @return mixed
     */
    public function clean($value, $config = null)
    {
        if (empty($value) || is_bool($value) || is_int($value) || is_float($value)) {
            return $value;
        }

        if (is_array($value)) {
            return array_map(function ($item) use ($config) {
                return $this->clean($item, $config);
            }, $value);
        }

        return $this->process($value, $config);
    }

    /**
     * @param string $value
     * @param mixed $config
     *
     * @return string
     */
    protected function process(string $value, $config = null)
    {
        return clean($value, $config);
    }

}
