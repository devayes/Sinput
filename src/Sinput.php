<?php

declare(strict_types=1);

namespace Devayes\Sinput;

use Devayes\Sinput\SinputAbstract;

class Sinput extends SinputAbstract
{
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
                $return[$key] = $value;
            }
        }

        if (! empty($return)) {
            $return = $this->clean($return, null, $config);
        }

        return $return;
    }

}
