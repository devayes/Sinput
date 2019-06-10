<?php

namespace Devayes\Sinput\Middleware;

use Closure;

class SinputMiddleware
{
    public function handle($request, Closure $next, $ruleset = null)
    {
        if ($request->keys()) {
            $ruleset = ($ruleset ?? config('sinput.middleware_ruleset'));
            $request->merge(sinput()->all($ruleset));
        }

        return $next($request);
    }
}
