<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AssistantLogin extends Controller
{


    public function login() {

        $lang = app()->getLocale();

        session()->put('locale',$lang);
        app()->setLocale($lang);

        if(Auth::guard('assistant')->check()) {

            return redirect('assistant_panel');

        } else {
            return view('assistant.layouts.login');
        }
    }

    public function login_post(Request $request) {

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $remember = $request->has('remember') ? true : false;

        if ( Auth::guard('assistant')->attempt([ 'email' => request('email') , 'password' => request('password')] , $remember) ) {


            return redirect('assistant_panel');

        } else {
            return redirect()->back()->with('error','Please Check Your User Name and Password again');
        }
    }

    public function logout(Request $request) {
        Auth::guard('assistant')->logout();
        return redirect('assistant_panel/login');
    }


}
