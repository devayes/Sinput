<?php

if (!function_exists('sinput')) {
    function sinput($input, $config = null)
    {
        return app('sinput')->clean($input, $config);
    }
}
