<?php

namespace Devayes\Sinput\Middleware;

use Closure;

class filterRequest
{
    public function handle($request, Closure $next)
    {
        if ($request->keys()) {
            $request->merge(sinput()->all(config('sinput.middleware_ruleset')));
        }
        
        return $next($request);
    }
}
