<?php

namespace App\Http\Middleware;

use Closure;

class Admin_Language
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $lang = session()->get('admin_lang');

        if($lang) {
            app()->setLocale($lang);
            session()->put('admin_lang',$lang);
        } else {
            app()->setLocale('ar');
            session()->put('admin_lang','ar');
        }

        return $next($request);
    }
}
