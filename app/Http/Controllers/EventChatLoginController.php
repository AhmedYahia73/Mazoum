<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\EventUsers;
use App\Models\EventUserActions;

class EventChatLoginController extends Controller
{



    public function login() {

      if (session()->has('event_login')) {
            //$arr = session('event_login');
        	//dd($arr);
        	return redirect('event-chat');

      } else {
      		return view('event.login');
      }

    }



    public function login_post(Request $request) {

        $request->validate([
          	'mobile_code' => 'required',
            'phone' => 'required|numeric',
        ]);

      	
      	// إزالة الأصفار من بداية رقم الهاتف
        $phone = ltrim($request->phone, '0');

        $mobile = $request->mobile_code . $phone;

      	$item = EventUsers::where('is_open','yes')->where('mobile',$mobile)->whereHas('event')->first();

      	if($item != null) {

             session(['event_login' => $item->event_id .'-'. $item->id.'-'. $item->mobile ]);
			 return redirect('event-chat');

        } else {
        	 return redirect('event/login')->with('error','هذا الرقم غير موجود مسبقا برجاء التحقق من رقم الموبيل');
        }


    }



  	public function logout() {

      session()->forget('event_login');
      return redirect('event/login');

    }




}
