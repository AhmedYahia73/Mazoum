<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;


class User
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $lang = app()->getLocale();

        if (! Auth::guard($guard)->check()) {
            return redirect(route($lang.'-login'));
        }

        return $next($request);
    }
}
