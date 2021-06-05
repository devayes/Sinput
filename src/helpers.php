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
 * Clean a variable or items in an array
 */
if (!function_exists('scrub')) {
    function scrub($var = null, $ruleset = null) {
        return app('sinput')->clean($var, $ruleset);
    }
}
