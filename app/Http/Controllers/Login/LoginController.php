<?php

namespace App\Http\Controllers\Login;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

use App\Models\Admin;
use App\Models\User;

class LoginController extends Controller
{
    public function login_admin(Request $request){
        $validation = Validator::make($request->all(), [
            'email' => 'required|email', 
            'password' => 'required', 
        ]);
        if ($validation->fails()) {
            return response()->json($validation->errors(), 422);
        }

        $user = Admin::
        where('email', $request->email)
        ->first(); 
        if ($user && password_verify($request->input('password'), $user->password)) {
            $user->token = $user->createToken('admin')->plainTextToken;
            return response()->json([
                'user' => $user,
                'token' => $user->token,
                "role" => "admin",
            ], 200);
        } 
        else {
            $user = User::
            where('email', $request->email)
            ->first();
            if ($user && password_verify($request->input('password'), $user->password)) {
                $user->token = $user->createToken($user->role)->plainTextToken;
                return response()->json([
                    'user' => $user,
                    'token' => $user->token,
                    "role" => $user->role,
                ], 200);
            } 
            return response()->json(['errors'=>'creational not Valid'],403);
        }
    }
}
