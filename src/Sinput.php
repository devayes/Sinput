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

use Illuminate\Http\Request;

/**
 * This is the Sinput class.
 *
 * @author Devin Hayes <devayes@gmail.com>
 */
class Sinput
{
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Create a new instance.
     *
     * @param \Illuminate\Http\Request              $request
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Get all of the input and files for the request.
     *
     * @param bool $trim
     * @param bool $clean
     *
     * @return array
     */
    public function all($config = null)
    {
        $values = $this->request->all();

        return $this->clean($values, $config);
    }

    /**
     * Get an input item from the request.
     *
     * @param string $key
     * @param mixed  $default
     * @param bool   $trim
     * @param bool   $clean
     *
     * @return mixed
     */
    public function get(string $key, $default = null, $config = null)
    {
        $value = $this->request->input($key, $default);

        return $this->clean($value, $config);
    }

    /**
     * Get an input item from the request.
     *
     * This is an alias to the get method.
     *
     * @param string $key
     * @param mixed  $default
     * @param bool   $trim
     * @param bool   $clean
     *
     * @return mixed
     */
    public function input(string $key, $default = null, $config = null)
    {
        return $this->get($key, $default, $config);
    }

    /**
     * Get a subset of the items from the input data.
     *
     * @param string|string[] $keys
     * @param bool            $trim
     * @param bool            $clean
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
     * Get all of the input except for a specified array of items.
     *
     * @param string|string[] $keys
     * @param bool            $trim
     * @param bool            $clean
     *
     * @return array
     */
    public function except($keys, $config = null)
    {
        $values = $this->request->except((array) $keys);

        return $this->clean($values, $config);
    }

    /**
     * Get a mapped subset of the items from the input data.
     *
     * @param string[] $keys
     * @param bool     $trim
     * @param bool     $clean
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
     * Get an old input item from the request.
     *
     * @param string $key
     * @param mixed  $default
     * @param bool   $trim
     * @param bool   $clean
     *
     * @return mixed
     */
    public function old(string $key, $default = null, $config = null)
    {
        $value = $this->request->old($key, $default);

        return $this->clean($value, $config);
    }

    /**
     * Clean a specified value or values.
     *
     * @param mixed $value
     * @param bool  $trim
     * @param bool  $clean
     *
     * @return mixed
     */
    public function clean($value, $config = null)
    {
        if (is_bool($value) || is_int($value) || is_float($value)) {
            return $value;
        }

        $final = null;

        if ($value !== null) {
            if (is_array($value)) {
                $all = $value;
                $final = [];
                foreach ($all as $key => $value) {
                    if ($value !== null) {
                        $final[$key] = $this->clean($value, $config);
                    }
                }
            } else {
                if ($value !== null) {
                    $final = $this->process((string) $value, $config);
                }
            }
        }

        return $final;
    }

    /**
     * Process a specified value.
     *
     * @param string $value
     * @param bool   $trim
     * @param bool   $clean
     *
     * @return string
     */
    protected function process(string $value, $config = null)
    {
        return clean($value, $config);
    }

}
