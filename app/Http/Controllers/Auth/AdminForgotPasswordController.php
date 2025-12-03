<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Password;
use Illuminate\Http\Request;

class AdminForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:admin');
    }
    
    
    public function showLinkRequestForm() {
        return view('admin.layouts.user_email');
    }
    
    

     
     public function sendResetLinkEmail(Request $request)
    {
        $this->validateEmail($request);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $response = $this->broker()->sendResetLink(
            $this->credentials($request)
        );

        return $response == Password::RESET_LINK_SENT
                    ? $this->sendResetLinkResponse($request, $response)
                    : $this->sendResetLinkFailedResponse($request, $response);
    }
    
    protected function validateEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
    }


    protected function credentials(Request $request)
    {
        return $request->only('email');
    }
    
    
    protected function sendResetLinkResponse(Request $request, $response)
    {
        return back()->with('success', trans($response));
    }


    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'This email does not exist' ]);
    }
    
    

    //defining which password broker to use, in our case its the admins
    protected function broker() {
        return Password::broker('admins');
    }


    
    
}
