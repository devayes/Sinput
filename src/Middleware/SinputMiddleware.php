<?php

namespace Devayes\Sinput\Middleware;

use Closure;

class SinputMiddleware
{
    /**
     * Handle request via Sinput midddleware
     *
     * EXAMPLES:
     * Apply default middleware ruleset to all input
     * Route::middleware(['sinput'])->group(function () { .. });
     * Allow html for all input
     * Route::middleware(['sinput:allow_html'])->group(function () { .. });
     * Remove html from foo and bar input
     * Route::middleware(['sinput:no_html,foo|bar'])->group(function () { .. });
     * No html allowed in foo, but allow html in bar
     * Route::middleware(['sinput:no_html,foo', 'sinput:allow_html,bar'])->group(function () { .. });
     *
     * @param Request $request
     * @param Closure $next
     * @param string|null $ruleset
     * @param string|null $fields
     * @return function
     */
    public function handle($request, Closure $next, ?string $ruleset = null, ?string $fields = null)
    {
        if ($request->keys()) {
            $ruleset = ($ruleset ?? config('sinput.middleware_ruleset'));
            $fields = explode('|', $fields);
            $request->merge(
                $request->scrub($fields, $ruleset)->all()
            );
        }

        return $next($request);
    }
}
