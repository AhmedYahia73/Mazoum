<?php

namespace App\Http\Controllers\Member;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Events;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{

  

     public function home()
     {
        $msg = trans('home.welcome_msg') . Auth::guard('member')->user()->name;

        return view('member.home',compact('msg'));
     }
  
  
  	 public function event_messages($id) {
     
     	$event = Events::findOrFail($id);
        return view('member.event_messages',compact('event'));
     }


}
