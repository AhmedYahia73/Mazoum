<?php

namespace App\Http\Controllers\Login;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

use App\Models\Admin;

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
        if (empty($user)) {
            return response()->json([
                'errors' => 'This Store Man does not have the ability to login'
            ], 405);
        }
        if (password_verify($request->input('password'), $user->password)) {
            $user->token = $user->createToken('admin')->plainTextToken;
            return response()->json([
                'user' => $user,
                'token' => $user->token,  
            ], 200);
        }
        else {
            return response()->json(['errors'=>'creational not Valid'],403);
        }
    }
}
