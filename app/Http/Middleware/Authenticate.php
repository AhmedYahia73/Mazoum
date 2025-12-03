<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class Authenticate extends Middleware
{
    /**
     * Handle unauthenticated requests for API.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function redirectTo($request)
    {
        // لو الطلب يتوقع JSON (API) → رجع response 401
        if ($request->expectsJson()) {
            abort(response()->json([
                'message' => 'Unauthenticated.'
            ], 401));
        }

        // لو Request عادي (Web) ممكن تحافظ على redirect
        return null;
    }
}
