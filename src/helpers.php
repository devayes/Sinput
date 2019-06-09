<?php

if ( ! function_exists('sinput')) {
    function sinput($input = null, $default = null, $config = null)
    {
        $sinput = app('sinput');

        if ( ! is_null($input)) {
            return $sinput->input($input, $default, $config);
        }

        return $sinput;
    }
}
