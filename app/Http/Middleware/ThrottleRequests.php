<?php

namespace App\Http\Middleware;

use Illuminate\Routing\Middleware\ThrottleRequests as MiddlewareThrottleRequests;
use Closure;

class ThrottleRequests extends MiddlewareThrottleRequests
{

    public function handle($request, Closure $next, $maxAttempts = 60, $decayMinutes = 1, $prefix = '')
    {
        if (app()->environment('local', 'testing')) {
            return $next($request);
        }

        parent::handle($request, $next, $maxAttempts, $decayMinutes, $prefix);
    }
}
