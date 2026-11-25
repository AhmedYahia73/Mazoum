<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait ResolvesChatActor
{
    /**
     * Resolve authenticated actor from Sanctum, default Auth, or Mazoum custom token header.
     *
     * @param Request $request
     * @return mixed
     */
    protected function resolveActor(Request $request)
    {
        $actor = auth('sanctum')->user() ?? auth('api')->user() ?? auth('admin')->user() ?? Auth::user();
        if ($actor) {
            return $actor;
        }

        $token = $request->header('token') ?? $request->bearerToken();
        if ($token) {
            $user = User::withoutGlobalScopes()->where('token', $token)->first();
            if ($user) {
                Auth::setUser($user);
                return $user;
            }

            $admin = Admin::where('remember_token', $token)->first();
            if ($admin) {
                Auth::setUser($admin);
                return $admin;
            }
        }

        return null;
    }

    /**
     * Determine whether the actor is an Admin or Employee.
     *
     * @param mixed $actor
     * @return bool
     */
    protected function isAdminOrEmployee($actor): bool
    {
        if (!$actor) {
            return false;
        }

        $role = $actor->role ?? ($actor->user_type ?? 'user');

        return in_array($role, ['admin', 'employee']);
    }
}
