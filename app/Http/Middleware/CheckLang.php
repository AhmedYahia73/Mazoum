<?php

namespace App\Http\Middleware;

use App\Traits\GeneralTrait;
use Closure;

class CheckLang
{

    use GeneralTrait;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    public function handle($request, Closure $next)
    {

        if(getallheaders() != null && ! empty(getallheaders())) {
          
          	if(array_key_exists("language",getallheaders())) {
            	$language = getallheaders()['language'];
            } elseif(array_key_exists("Language",getallheaders())) {
            	$language = getallheaders()['Language'];
            } else {
            	$language = 'ar';
            }

            if($language != null && ($language == 'ar' || $language == 'en')) {
                return $next($request);
            } else {
                return $this->returnError('E100','please choose right language');
            }

        } else {
            return $this->returnError('E100','sorry try again in another time #000');
        }


    }


}
