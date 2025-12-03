<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AdminLogin extends Controller
{


    public function login() {

        $lang = app()->getLocale();

        session()->put('locale',$lang);
        app()->setLocale($lang);

        if(Auth::guard('admin')->check()) {

            return redirect('admin');

        } else {
            return view('admin.layouts.login');
        }
    }

    public function login_post(Request $request) {

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $remember = $request->has('remember') ? true : false;

        if ( Auth::guard('admin')->attempt([ 'email' => request('email') , 'password' => request('password')] , $remember) ) {


            return redirect('admin');

        } else {
            return redirect()->back()->with('error','Please Check Your User Name and Password again');
        }
    }

    public function logout(Request $request) {
        Auth::guard('admin')->logout();
        return redirect('admin/login');
    }


}
