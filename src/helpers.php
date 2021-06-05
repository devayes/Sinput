<?php

/**
 * Access the Sinput object
 */
if (!function_exists('sinput')) {
    function sinput($ruleset = null) {
        return app('sinput')->setRuleset($ruleset);
    }
}
/**
 *
 */
if (!function_exists('scrub')) {
    function scrub($var = null, $config = null) {
        return app('sinput')->clean($var, $config);
    }
}
