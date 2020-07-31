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
    public function cookie(string $key, $default = null, $config = null)
    {
        return $this->clean(
            $this->request->cookie($key, $default),
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
    public function only($keys, $config = null): array
    {
        $method = $this->getMethod();
        $values = [];
        foreach ((array)$keys as $key) {
            $values[$key] = $this->$method($key, null, $config);
        }

        return $values;
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
            $this->request->except((array) $keys),
            null,
            $config
        );
    }

    /**
     * @param string $key
     * @param mixed $default
     * @param mixed $config
     *
     * @return mixed
     */
    public function old(string $key, $default = null, $config = null): string
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
        $method = $this->getMethod();
        if (is_array($keys)) {
            $return = [];
            foreach ($keys as $index) {
                array_push($return, $this->$method($index, null, $config));
            }
            return $return;
        } elseif (is_string($keys)) {
            return (array)$this->$method($keys, null, $config);
        }

        return null;
    }

    /**
     * Ex: Sinput::match("#^perm_#"); matches: perm_thing, perm_stuff
     * @param  string     $regex
     * @param  mixed    $config
     * @return array
     */
    public function match($regex, $config = null): array
    {
        $return = [];
        foreach ($this->request->all() as $key => $value) {
            if (preg_match($regex, $key)) {
                $return[$key] = $this->clean($value, null, $config);
            }
        }

        return $return;
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
     * @param mixed $key
     * @return boolean
     */
    public function exists($key): bool
    {
        return $this->request->has($key);
    }

}
