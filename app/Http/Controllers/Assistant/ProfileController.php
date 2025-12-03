<?php

namespace App\Http\Controllers\Assistant;

use Illuminate\Http\Request;
use App\Http\Requests\Assistant as modelRequest;
use App\Http\Controllers\Controller;
use App\Models\User as Model;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function home()
    {
        $msg = trans('home.welcome_msg') . Auth::guard('assistant')->user()->name;

        return view('assistant.layouts.home', compact('msg'));
    }

    public function profile()
    {
        $admin = Auth::guard('assistant')->user();
        $Item  = User::where('id', $admin->id)->first();

        return view('assistant.profile.home', compact('Item'));
    }


    public function update_profile(Request $request)
    {
        $admin = Auth::guard('assistant')->user();
        $Item = Assistant::where('id', $admin->id)->first();

        $Item->update($this->gteInput($request, $Item));
        return redirect()->back()->with('info', trans('home.update_msg'));
    }


    private function gteInput($request, $modelClass)
    {

        $input = $request->only(['name','email' ,'mobile']);

        if(isset($modelClass)) {

            if($request->password == null) {
                $input['password'] =  $modelClass->password;
            } else {
                $input['password'] =  bcrypt($request->password);
            }

        } else {
            $input['password'] =  bcrypt($request->password);
        }

        return  $input;
    }





}
