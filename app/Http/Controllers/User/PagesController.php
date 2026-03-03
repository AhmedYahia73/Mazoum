<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\Subscribers;


class PagesController extends Controller
{


    // home
    public function home()
    {
        $lang = app()->getLocale();

        return view('user.layouts.home');
    }

    public function terms_condition()
    {
        return view('user.pages.terms_condition');
    }


    public function privacy_policy()
    {
        return view('user.pages.privacy');
    }


    public function about()
    {
        return view('user.pages.about-us');
    }


    public function portfolio()
    {
        return view('user.pages.portfolio');
    }

    public function services()
    {
        return view('user.pages.services');
    }

    public function pricing()
    {
        return view('user.pages.pricing');
    }

    public function view_contact_us()
    {
        return view('user.pages.contact-us');
    }



    public function save_subscribe(Request $request) {

        $lang = app()->getLocale();
       $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        $count = Subscribers::where('email',$request->email)->count();

        if($count == 0) {
            Subscribers::create([
                'email' => $request->email
            ]);
        }

        if($lang == 'en') {
            return response()->json([
                'success' =>'you subscribed successfully'
            ]);
        } else {
            return response()->json([
                'success' =>' تم الأشتراك بنجاح'
            ]);
        }

    }

}
