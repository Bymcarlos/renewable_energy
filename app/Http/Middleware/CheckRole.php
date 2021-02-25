<?php

namespace CtoVmm\Http\Middleware;

use Closure;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    //To pass an array to this middleware, need roles param be an ellipsis parameter 
    //https://stackoverflow.com/questions/43685610/pass-an-array-to-a-middleware-in-laravel
    public function handle($request, Closure $next, ...$roles)
    {
        if (! $request->user()->hasAnyRole($roles)) {
            return redirect('home');
        }
        return $next($request);
    }
}
