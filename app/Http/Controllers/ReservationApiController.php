<?php

namespace App\Http\Controllers;

use App\Models\Orders;
use App\Models\Admin;
use App\Models\Setting;
use App\Models\Logs;
use App\Models\EventUsers;
use App\Models\Qr_Code;
use App\Models\EventUserLogs;
use App\Models\Events;
use App\Models\EventMessages;
use App\Models\Parking;
use App\Models\Reservation;
use App\Models\CongratulationMessages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Intervention\Image\ImageManagerStatic as Image;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use GuzzleHttp\Client;

class ReservationApiController extends Controller
{
    
    

    
    public function confirm_reservation(Request $request)
    {
        info('confirm_reservation');
        info($request->all());
        
        $setting = Setting::first();
        
        $mobile = ltrim($request->phone,"+");
        
        $item = Reservation::where('mobile',$mobile)->orderBy('id','desc')->first();
        
        if($item) {
            
            $item->update(['status' => 'confirmed']);
        }
    }
  
  
  
  
  	public function cancel_reservation(Request $request)
    {
        info('cancel_reservation');
        info($request->all());
        
        $setting = Setting::first();
        
        $mobile = ltrim($request->phone,"+");
        
        $item = Reservation::where('mobile',$mobile)->orderBy('id','desc')->first();
        
        if($item) {
            
            $item->update(['status' => 'canceld']);
        }
    }
    
    
   





   



}
