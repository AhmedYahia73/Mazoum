<?php

namespace App\Http\Middleware;

use App\Traits\GeneralTrait;
use Closure;
use Illuminate\Http\Request;

class CheckPassword
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
        //return request()->header();
        //return getallheaders();


        if (getallheaders() != null && ! empty(getallheaders())) {
          	          	
          	if(array_key_exists("api_password",getallheaders())) {
				$api_password = getallheaders()['api_password'];            
            } elseif(array_key_exists("Password",getallheaders())) {
            	$api_password = getallheaders()['Password'];      
            } else {
            	$api_password = null;	
            }

            if ($api_password != env('API_PASSWORD', '123456')) {
                return $this->returnError('401', 'Unauthorized User #1237');
            }

            return $next($request);
        } else {
            return $this->returnError('E100', 'sorry try again in another time #000');
        }
    }
}
