<?php

/**
 * Access the Sinput object or request input by index
 */
if (!function_exists('sinput')) {
    function sinput($input = null, $default = null, $ruleset = null)
    {
        $sinput = app('sinput');
        if (!is_null($input)) {
            return $sinput->input($input, $default, $ruleset);
        }
        return $sinput;
    }
}
/**
 * Clean a variable or items in an array
 */
if (!function_exists('scrub')) {
    function scrub($var = null, $ruleset = null)
    {
        return app('sinput')->clean($var, $ruleset);
    }
}
/**
 * Decode html entities
 */
if (!function_exists('sinput_decode')) {
    function sinput_decode($var)
    {
        return \Devayes\Sinput\Sinput::decode($var);
    }
}
