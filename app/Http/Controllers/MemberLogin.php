<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class MemberLogin extends Controller
{


    public function login() {

        if(Auth::guard('member')->check()) {

            return redirect('member_panel');

        } else {
            return view('member.login');
        }
    }

    public function login_post(Request $request) {

        $request->validate([
            'mobile' => 'required|numeric',
            'password' => 'required',
        ]);

        $remember = $request->has('remember') ? true : false;

        if ( Auth::guard('member')->attempt([ 'mobile' => request('mobile') , 'password' => request('password')] , $remember) ) {


            return redirect('member_panel');

        } else {
            return redirect()->back()->with('error','Please Check Your mobile and Password again');
        }
    }

    public function logout(Request $request) {
        Auth::guard('member')->logout();
        return redirect('member_panel/login');
    }


}
