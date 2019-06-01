<?php

declare(strict_types=1);

namespace Devayes\Sinput;

use Devayes\Sinput\SinputAbstract;

class Sinput extends SinputAbstract
{

    /**
     * @param mixed $config
     *
     * @return array
     */
    public function all($config = null)
    {
        $values = $this->request->all();

        return $this->clean($values, null, $config);
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

        return $this->clean($value, null, $config);
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

        return $this->clean($values, null, $config);
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

        return $this->clean($value, null, $config);
    }

    /**
     * @param string $keys
     * @param mixed $config
     *
     * @return mixed
     */
    public function list($keys, $config = null)
    {
        if (is_array($keys)) {
            $return = [];
            foreach ($keys as $index) {
                array_push($return, $this->get($index, null, $config));
            }
            return $return;
        } elseif (is_string($keys)) {
            return (array)$this->get($keys, null, $config);
        }

        return null;
    }

    /**
     * Ex: Sinput::match("#^perm_#"); matches: perm_thing, perm_stuff
     * @param  string     $regex
     * @param  mixed    $config
     * @return array
     */
    public function match($regex, $config = null)
    {
        $return = [];
        foreach ($this->request->all() as $key => $value) {
            if (preg_match($regex, $key)) {
                $return[$key] = $this->clean($value, null, $config);
            }
        }

        return $return;
    }

}
