<?php

namespace Devayes\Sinput\Middleware;

use Closure;

class SinputMiddleware
{
    // Apply default middleware ruleset to all input
    // Route::middleware(['sinput'])->group(function () { .. });
    // Allow html for all input
    // Route::middleware(['sinput:allow_html'])->group(function () { .. });
    // Remove html from foo and bar input
    // Route::middleware(['sinput:no_html,foo|bar'])->group(function () { .. });
    // No html allowed in foo, but allow html in bar
    // Route::middleware(['sinput:no_html,foo', 'sinput:allow_html,bar'])->group(function () { .. });
    public function handle($request, Closure $next, $ruleset = null, $fields = null)
    {
        if ($request->keys()) {
            $ruleset = ($ruleset ?? config('sinput.middleware_ruleset'));
            $fields = (is_string($fields) ? explode('|', $fields) : $fields);
            $request->merge(
                $request->scrub(
                    (is_string($fields) ? explode('|', $fields) : $fields),
                    $ruleset
                )->all()
            );
        }

        return $next($request);
    }
}
