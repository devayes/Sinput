<?php

/**
 * Access the Sinput object or request input by index
 * eg: $foo = sinput('foo', $default, $ruleset);
 * eg: $input = sinput()->only(['foo', 'bar], $ruleset);
 */
if (!function_exists('sinput')) {
    /**
     * Filter request input or get Sinput class object.
     *
     * @param mixed $input
     * @param mixed $default
     * @param string|null $ruleset
     * @return mixed
     */
    function sinput($input = null, $default = null, ?string $ruleset = null)
    {
        static $sinput = null;

        if (is_null($sinput)) {
            $sinput = app('sinput');
        }

        if (!is_null($input)) {
            return $sinput->input($input, $default, $ruleset);
        }
        return $sinput;
    }
}

if (!function_exists('scrub')) {
    /**
     * Scrub an existing variable or array
     *
     * @param mixed $var
     * @param string|null $ruleset
     * @return mixed
     */
    function scrub($var, ?string $ruleset = null)
    {
        return sinput()->clean($var, $ruleset);
    }
}
