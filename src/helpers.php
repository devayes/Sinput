<?php

if (!function_exists('sinput')) {
    function sinput($input, $default = null, $config = null)
    {
        return app('sinput')->get($input, $default, $config);
    }
}
