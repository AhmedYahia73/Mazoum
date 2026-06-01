<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;

class ProfileController extends Controller
{
    public function profile(Request $request){
        return response()->json([
            "id" => auth()->user()->id,
            "name" => auth()->user()->name,
            "email" => auth()->user()->email,
            "mobile" => auth()->user()->mobile,
            "mobile_code" => auth()->user()->mobile_code,
        ]);
    }

    public function update_profile(Request $request){
        User::
        where("id", $request->user()->id)
        ->update([
            "name" => $request->name ?? $request->user()->name,
            "email" => $request->email ?? $request->user()->email,
            "mobile" => $request->mobile ?? $request->user()->mobile,
            "mobile_code" => $request->mobile_code ?? $request->user()->mobile_code,
        ]);
        
        return response()->json([
            "success" => "You update data success"
        ]);
    } 
}
