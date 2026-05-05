<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\CongratulationMessages;
use App\Models\EventMessages;
use App\Models\Events;
use App\Models\EventUserActions;
use App\Models\EventUserLogs;
use App\Models\EventUsers;
use App\Models\Logs;
use App\Models\Notifications;
use App\Models\Orders;
use App\Models\Parking; 
use App\Models\Qr_Code;
use App\Models\Setting;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManagerStatic as Image;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class EventUserActionsController extends Controller
{


    public function event_login($code) {

        $event_user = EventUsers::
        where('code', $code)
        ->with("event")
        ->firstOrFail();
        $check_receive_apology = EventUserActions::
        where('action', 'refuse_event')
        ->where('event_user_id',$event_user->id)->first();
        $accept_event = EventUserActions::
        where('event_user_id', $event_user->id)
        ->where('action','accept_event')
        ->first();
        $btns_status = true;

        if($check_receive_apology && $accept_event &&
        $check_receive_apology->created_at < $accept_event->created_at ){
            $qr_row = Qr_Code::
            where('event_user_id',$event_user->id)
            ->first();
            $btns_status = false;
        }
        else{ 
            $qr_row = !$check_receive_apology ? Qr_Code::where('event_user_id',$event_user->id)->first() : null;  
        }      

                        //   CongratulationMessages::create([
                        //     'event_id' => $event->id,
                        //     'message_id' => $request->message_id,
                        //     'type' => '',
                        //     'name' => $user->name,
                        //     'mobile' => $user->mobile,
                        //     'message' => $request->message
                        //   ]);
    
        // $yes_receive_apology = EventUserActions::
        // where('event_user_id', $event_user->id)
        // ->where('action','yes_receive_apology')
        // ->first();
        
        $apologize_messages = EventMessages::
        whereHas('event', function ($event) {
            $event->whereIn('is_open', ['yes', 'current']);
        })
        ->where("event_user_id", $event_user->id)
        ->with("reply")
        ->first();
        if($check_receive_apology && !$apologize_messages){
            $apologize_messages = [
                "created_at" => "2026-03-27T00:03:47.000000Z",
                "event_id" => 1906,
                "event_user_id" => 47090,
                "id" => 1903,
                "message" => "",
                "message_id" => null,
                "mobile" => "966582836463",
                "name" => "",
                "reply" => [],
                'type' => "new",
                'updated_at => "2025-05-05' 
            ];
        }
        $yes_receive_congratulation = CongratulationMessages::
        with("reply")
        ->whereHas('event', function ($event) {
            $event->whereIn('is_open', ['yes', 'current']);
        }) 
        ->where("event_user_id", $event_user->id)
        ->first();
        

        $yes_reply_congratulation = CongratulationMessages::
        where("message_id", $event_user->id)
        ->first();
        if($btns_status){

            $url = $qr_row->qr_link;

            $exists = Http::head($url)->successful();

            if (!$exists) {
                $image_name = $qr_row->uu_id . '-test-qr.png';
                $this->update_qr($event_user->event, $qr_row->uu_id, $event_user, $image_name);
                $qr_row->update([
                    "qr" => $image_name,
                ]);
            }  
        }
        return response()->json([
            "event_user" => $event_user,
            "qr_row" => $qr_row,
            "accept_event" => $accept_event,
            "yes_receive_congratulation" => $yes_receive_congratulation,
            // "yes_receive_apology" => $yes_receive_apology,
            "yes_reply_congratulation" => $yes_reply_congratulation?->message,
            "apologize_messages" => $apologize_messages,
            "check_receive_apology" => !empty($check_receive_apology),
            "show_btns_status" => $btns_status,
        ]);

    }


    public function new_save_event_action(Request $request) {

        $validator = Validator::make($request->all(), [
            'event_user_id' => 'required',
            'code'          => 'required',
          	'action'        => 'required|in:accept_event,refuse_event,yes_receive_congratulation',
            'users_count'   => 'required_if:action,accept_event',
            'msg'           => 'nullable',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }   
        
      	$user_event = EventUsers::where('id',$request->event_user_id)->where('code',$request->code)->firstOrFail();

      	if($user_event != null && $user_event->event) {

            if($request->action == 'accept_event') {
                $users_count = $request->users_count;
                $event_action = EventUserActions::
                where("event_id", $user_event->event_id)
                ->where("event_user_id", $user_event->id)
                ->where("action", "accept_event")
                ->first(); 
                $user_event
                ->update(["accept_count" => $request->users_count + $user_event->accept_count]);
                if ($event_action) {
                    $users_count += ($event_action->users_count ?? 0);
                    $event_action->users_count += $request->users_count;
                    $event_action->save();
                } else {
                    EventUserActions::create([
                    'event_id' => $user_event->event_id,
                    'event_user_id' => $user_event->id,
                    'mobile' => $user_event->mobile,
                    'action' => 'accept_event',
                    'users_count' => $request->users_count,
                    'msg' => null
                    ]);
                }
                

                //////////////////////////////////////////////////

                $event = Events::find($user_event->event_id);

                Notifications::create([
                    'add_by'         => 'admin',
                    'user_id'        => 1,
                    'send_to_type'   => 'user',
                    'send_to_id'     => $user_event?->event?->user_id,
                    'en_title'       => 'accept event : ' . $user_event?->event?->title,
                    'ar_title'       => 'قبول الدعوه  : ' . $user_event?->event?->title,
                    'en_description' => 'user : ' . $user_event->name . ' accept event : ' . $user_event?->event?->title,
                    'ar_description' => 'المستخدم : ' . $user_event->name . ' قبل الدعوه  : ' . $user_event?->event?->title,
                    'type'           => 'event',
                    'item_id'        => $user_event?->event?->id,
                    'user_event_id'  => $user_event != null ? $user_event->id : 0,
                    'status'         => 'accept_event',
                ]);

                $user_event->update(['confirmed_at' => now(),'status' => 'attend' ]);

                $user_event->update([
                    "accept_count" => $users_count
                ]);
                if($user_event->users_count <= $users_count){
                    $user_event->update([
                        'is_accepted' => 'yes',
                    ]);
                }
                if($event->showing_qr == 'yes') {

                    $uu_id = $this->unique_uu_id();
                    $bg = 'qr-image-v9.jpg';

                    $image_name = $uu_id . '-test-qr.png';

                    $qr_row = Qr_Code::
                    where("event_user_id", $user_event->id)
                    ->first();
                    if($qr_row){
                        $qr_row
                        ->update([
                            "qr" => $image_name
                        ]);
                    }
                    else{
                        Qr_Code::create([
                            'event_user_id' => $user_event->id,
                            'event_id' => $user_event->event_id,
                            'qr' => $image_name,
                            'uu_id' => $uu_id,
                            'counter' => 0
                        ]);
                    }

                    // new code
                    $this->update_qr($event,$uu_id,$user_event,$image_name,$users_count);

                    $user_event->update([ 'qr_sent' => 'yes'  ]);

                    EventUserLogs::create([
                        'log' => "تم ارسال ال QR Code",
                        'event_id' => $user_event->event_id,
                        'event_user_id' => $user_event->id,
                        'message_id' => $user_event->message_id,
                        'status' => 'attend',
                        'error_title' => null,
                        'error_details' => null,
                    ]);

                }

            } elseif($request->action == 'refuse_event') {
                $user_event_count = $user_event->users_count;
                $event_action = EventUserActions::
                where("event_id", $user_event->event_id)
                ->where("event_user_id", $user_event->id)
                ->first(); 
                if($event_action && $user_event_count - $event_action->users_count < $request->users_count){
                    $refuse_count = $request->users_count - ($user_event_count - $event_action->users_count);
                    $event_action->users_count -= $refuse_count;
                    $event_action->save();
                }
                EventUserActions::create([
                   'event_id' => $user_event->event_id,
                   'event_user_id' => $user_event->id,
                   'mobile' => $user_event->mobile,
                   'action' => 'refuse_event',
                   'msg' => $request->msg
                ]);

                //////////////////////////////////////////////////////////////

                Notifications::create([
                    'add_by'         => 'admin',
                    'user_id'        => 1,
                    'send_to_type'   => 'user',
                    'send_to_id'     => $user_event?->event?->user_id,
                    'en_title'       => 'refuse event : ' . $user_event?->event?->title,
                    'ar_title'       => 'رفض الدعوه  : ' . $user_event?->event?->title,
                    'en_description' => 'user : ' . $user_event->name . ' refuse event : ' . $user_event?->event?->title,
                    'ar_description' => 'المستخدم : ' . $user_event->name . ' رفض الدعوه  : ' . $user_event?->event?->title,
                    'type'           => 'event',
                    'item_id'        => $user_event?->event?->id,
                    'user_event_id'  => $user_event != null ? $user_event->id : 0,
                    'status'         => 'refuse_event',
                ]);

                Qr_Code::where('event_user_id', $user_event->id)->delete();

                $user_event->update([ 'scan' => null , 'scan_at' => null, 'is_refused' => 'yes','is_accepted' => 'no' ,'status' => 'not-attend'  ]);

                //////////////////////////////////////////////////////////////

                $check_receive_apology = EventUserActions::where('event_user_id',$user_event->id)->where('action','yes_receive_apology')->first();

                if($check_receive_apology == null && $request->msg != null) {

                    EventUserActions::create([
                        'event_id' => $user_event->event_id,
                        'event_user_id' => $user_event->id,
                        'mobile' => $user_event->mobile,
                        'action' => 'yes_receive_apology',
                        'msg' => $request->msg,
                    ]);

                    EventMessages::create([
                        'event_id' => $user_event != null ? $user_event->event_id : 0,
                        'event_user_id' => $user_event != null ? $user_event->id : 0,
                        'name' => $user_event != null ? $user_event->name : '',
                        'mobile' => $user_event->mobile,
                        'message' => $request->msg
                    ]);

                    Notifications::create([
                        'add_by'         => 'admin',
                        'user_id'        => 1,
                        'send_to_type'   => 'user',
                        'send_to_id'     => $user_event?->event?->user_id,
                        'en_title'       => 'new apology msg to event : ' . $user_event?->event?->title,
                        'ar_title'       => 'اعتذار جديد للدعوه   : ' . $user_event?->event?->title,
                        'en_description' => 'user : ' . $user_event->name . ' send apology message : ' . $request->msg,
                        'ar_description' => 'المستخدم : ' . $user_event->name . '  أرسل الأعتذار  : ' . $request->msg,
                        'type'           => 'event-msg',
                        'item_id'        => $user_event?->event?->id,
                        'user_event_id'  => $user_event != null ? $user_event->id : 0,
                        'status'         => 'new_msg',
                      ]);

                }

            } elseif($request->action == 'yes_receive_congratulation') {

                 $check_receive_congratulation = EventUserActions::where('event_user_id',$user_event->id)->where('action','yes_receive_congratulation')->first();

                if($check_receive_congratulation == null && $request->msg != null) {

                    EventUserActions::create([
                        'event_id' => $user_event->event_id,
                        'event_user_id' => $user_event->id,
                        'mobile' => $user_event->mobile,
                        'action' => 'yes_receive_congratulation',
                        'msg' => $request->msg,
                    ]);

                    CongratulationMessages::create([
                        'event_id' => $user_event != null ? $user_event->event_id : 0,
                        'event_user_id' => $user_event != null ? $user_event->id : 0,
                        'name' => $user_event != null ? $user_event->name : '',
                        'mobile' => $user_event->mobile,
                        'message' => $request->msg
                    ]);

                    Notifications::create([
                        'add_by'         => 'admin',
                        'user_id'        => 1,
                        'send_to_type'   => 'user',
                        'send_to_id'     => $user_event?->event?->user_id,
                        'en_title'       => 'new congratulation msg to event : ' . $user_event?->event?->title,
                        'ar_title'       => 'تهنئه جديده للدعوه   : ' . $user_event?->event?->title,
                        'en_description' => 'user : ' . $user_event->name . ' send congratulation message : ' . $request->msg,
                        'ar_description' => 'المستخدم : ' . $user_event->name . '  أرسل التهنئة  : ' . $request->msg,
                        'type'           => 'event-msg',
                        'item_id'        => $user_event?->event?->id,
                        'user_event_id'  => $user_event != null ? $user_event->id : 0,
                        'status'         => 'new_msg',
                    ]);

                }

            }

            return response()->json([
                "success" => "You add data success"
            ]);

        } else {
            abort(404);
        }

    }



    public function save_event_action(Request $request) {

        $request->validate([
            'event_user_id' => 'required',
            // 'code'          => 'required',
          	'action'        => 'required',
            'msg'           => 'required_if:action,save_msg',
        ]);

        $setting = Setting::first();

      	$user_event = EventUsers::where('id',$request->event_user_id)->first();

      	if($user_event != null && $user_event->event) {

          	if($request->action != 'save_msg') {

            	EventUserActions::create([
                   'event_id' => $user_event->event_id,
                   'event_user_id' => $user_event->id,
                   'mobile' => $user_event->mobile,
                   'action' => $request->action,
                   'msg' => null
               ]);

               /////////////////////////////////////// Start Accept Event ///////////////////////////////////////

                if($request->action == 'accept_event') {

                    $event = Events::find($user_event->event_id);

                    Notifications::create([
                        'add_by'         => 'admin',
                        'user_id'        => 1,
                        'send_to_type'   => 'user',
                        'send_to_id'     => $user_event?->event?->user_id,
                        'en_title'       => 'accept event : ' . $user_event?->event?->title,
                        'ar_title'       => 'قبول الدعوه  : ' . $user_event?->event?->title,
                        'en_description' => 'user : ' . $user_event->name . ' accept event : ' . $user_event?->event?->title,
                        'ar_description' => 'المستخدم : ' . $user_event->name . ' قبل الدعوه  : ' . $user_event?->event?->title,
                        'type'           => 'event',
                        'item_id'        => $user_event?->event?->id,
                        'user_event_id'  => $user_event != null ? $user_event->id : 0,
                        'status'         => 'accept_event',
                    ]);

                    $user_event->update([ 'is_accepted' => 'yes' ,'confirmed_at' => now(),'status' => 'attend' ]);

                    if($event->showing_qr == 'yes') {

                        $uu_id = $this->unique_uu_id();
                        $bg = 'qr-image-v9.jpg';

                        $image_name = $uu_id . '-test-qr.png';

                        Qr_Code::create([
                            'event_user_id' => $user_event->id,
                            'event_id' => $user_event->event_id,
                            'qr' => $image_name,
                            'uu_id' => $uu_id,
                            'counter' => 0
                        ]);

                        // new code
                        $this->update_qr($event,$uu_id,$user_event,$image_name);

                        // $qr_code_path = 'qr_code/' . $image_name;
                        // $link = asset('scan-qr/' . $uu_id);
                        // QrCode::size(900)->format('png')->generate($link, $qr_code_path);

                        // Image::make($bg)->insert($qr_code_path, 'left', 480, 0)->widen(700)->save($qr_code_path, 100);

                        // $destination = public_path($qr_code_path);

                        // $new_img = Image::make($destination);

                        // $new_img->text($user_event->users_count, 150, 615, function ($font) {
                        //     $font->file(public_path('font/OpenSans-Italic.ttf'));
                        //     $font->size(40);
                        //     $font->color('#eeb534');
                        // });

                        //     $new_img->text($user_event->mobile, 190, 680, function ($font) {
                        //     $font->file(public_path('font/OpenSans-Italic.ttf'));
                        //     $font->size(30);
                        //     $font->color('#000');
                        //     //$font->align('right'); // Adjust alignment if necessary
                        // });

                        // $new_img->save($destination);

                        $user_event->update([ 'qr_sent' => 'yes'  ]);

                        EventUserLogs::create([
                            'log' => "تم ارسال ال QR Code",
                            'event_id' => $user_event->event_id,
                            'event_user_id' => $user_event->id,
                            'message_id' => $user_event->message_id,
                            'status' => 'attend',
                            'error_title' => null,
                            'error_details' => null,
                        ]);

                    }

                }

                /////////////////////////////////////// End Accept Event ///////////////////////////////////////


                /////////////////////////////////////// Start Refuse Event ///////////////////////////////////////

                elseif($request->action == 'refuse_event') {

                    Notifications::create([
                        'add_by'         => 'admin',
                        'user_id'        => 1,
                        'send_to_type'   => 'user',
                        'send_to_id'     => $user_event?->event?->user_id,
                        'en_title'       => 'refuse event : ' . $user_event?->event?->title,
                        'ar_title'       => 'رفض الدعوه  : ' . $user_event?->event?->title,
                        'en_description' => 'user : ' . $user_event->name . ' refuse event : ' . $user_event?->event?->title,
                        'ar_description' => 'المستخدم : ' . $user_event->name . ' رفض الدعوه  : ' . $user_event?->event?->title,
                        'type'           => 'event',
                        'item_id'        => $user_event?->event?->id,
                        'user_event_id'  => $user_event != null ? $user_event->id : 0,
                        'status'         => 'refuse_event',
                    ]);

                    Qr_Code::where('event_user_id', $user_event->id)->delete();

                    $user_event->update([ 'scan' => null , 'scan_at' => null, 'is_refused' => 'yes','is_accepted' => 'no' ,'status' => 'not-attend'  ]);

                }

                /////////////////////////////////////// End Refuse Event ///////////////////////////////////////


                /////////////////////////////////////// Start Resend Qr ///////////////////////////////////////

                elseif($request->action == 'not_received_qr') {

                    EventUserActions::create([
                        'event_id' => $user_event->event_id,
                        'event_user_id' => $user_event->id,
                        'mobile' => $user_event->mobile,
                        'action' => 'resend_qr',
                        'msg' => null
                    ]);

                    $event = Events::find($user_event->event_id);

                    $user_event->update([ 'is_accepted' => 'yes'  ]);

                    if($event != null && $event->showing_qr == 'yes') {

                        $check_Qr_Code = Qr_Code::where('event_id',$user_event->event_id)->where('event_user_id',$user_event->id)->first();

                        if($check_Qr_Code) {

                            $uu_id = $check_Qr_Code->uu_id;

                            $image_name = $uu_id . '-test-qr.png';

                            $link = asset('scan-qr/' . $uu_id);
                            // $link = asset('mobile-scan-qr/' . $uu_id);
                            $qr_code_path = 'qr_code/' . $image_name;

                        } else {

                            $uu_id = $this->unique_uu_id();

                            $image_name = $uu_id . '-test-qr.png';

                            $check_Qr_Code = Qr_Code::create([
                                'event_user_id' => $user_event->id,
                                'event_id' => $user_event->event_id,
                                'qr' => $image_name,
                                'uu_id' => $uu_id,
                                'counter' => 0
                            ]);

                            $image_name = $uu_id . '-test-qr.png';

                            // new code
                            $this->update_qr($event,$uu_id,$user_event,$image_name);

                            $qr_code_path = 'qr_code/' . $image_name;

                            // $bg = 'qr-image-v9.jpg';

                            // $link = asset('scan-qr/' . $uu_id);
                            // QrCode::size(900)->format('png')->generate($link, $qr_code_path);

                            // Image::make($bg)->insert($qr_code_path,  'left', 480, 0)->widen(700)->save($qr_code_path, 100);

                            // $destination = public_path($qr_code_path);

                            // $new_img = Image::make($destination);

                            // $new_img->text($user_event->users_count, 150, 615, function ($font) {
                            //     $font->file(public_path('font/OpenSans-Italic.ttf'));
                            //     $font->size(40);
                            //     $font->color('#eeb534');
                            // });

                            // $new_img->save($destination);

                        }

                        $user_event->update([ 'qr_sent' => 'yes'  ]);

                        EventUserLogs::create([
                            'log' => "تم ارسال ال QR Code",
                            'event_id' => $user_event->event_id,
                            'event_user_id' => $user_event->id,
                            'message_id' => $user_event->message_id,
                            'status' => 'attend',
                            'error_title' => null,
                            'error_details' => null,
                        ]);

                    }

                }

                /////////////////////////////////////// End Resend Qr ///////////////////////////////////////



                /////////////////////////////////////// Start Receive Congratulation ///////////////////////////////////////

                elseif($request->action == 'location_event') {

                    $user_event->update([ 'get_location' => 'yes' ]);

                }

                /////////////////////////////////////// End Receive Congratulation ///////////////////////////////////////


            } else {

                $check_receive_congratulation = EventUserActions::where('event_user_id',$user_event->id)->where('action','yes_receive_congratulation')->first();
                $check_receive_apology = EventUserActions::where('event_user_id',$user_event->id)->where('action','yes_receive_apology')->first();

                if(($check_receive_congratulation != null && $check_receive_apology == null) || ($check_receive_congratulation != null && $check_receive_apology != null && $check_receive_congratulation->id > $check_receive_apology->id)) {

                    EventUserActions::create([
                        'event_id' => $user_event->event_id,
                        'event_user_id' => $user_event->id,
                        'mobile' => $user_event->mobile,
                        'action' => 'yes_receive_congratulation',
                        'msg' => $request->msg,
                    ]);

                    CongratulationMessages::create([
                        'event_id' => $user_event != null ? $user_event->event_id : 0,
                        'event_user_id' => $user_event != null ? $user_event->id : 0,
                        'name' => $user_event != null ? $user_event->name : '',
                        'mobile' => $user_event->mobile,
                        'message' => $request->msg
                    ]);

                    Notifications::create([
                        'add_by'         => 'admin',
                        'user_id'        => 1,
                        'send_to_type'   => 'user',
                        'send_to_id'     => $user_event?->event?->user_id,
                        'en_title'       => 'new congratulation msg to event : ' . $user_event?->event?->title,
                        'ar_title'       => 'تهنئه جديده للدعوه   : ' . $user_event?->event?->title,
                        'en_description' => 'user : ' . $user_event->name . ' send congratulation message : ' . $request->msg,
                        'ar_description' => 'المستخدم : ' . $user_event->name . '  أرسل التهنئة  : ' . $request->msg,
                        'type'           => 'event-msg',
                        'item_id'        => $user_event?->event?->id,
                        'user_event_id'  => $user_event != null ? $user_event->id : 0,
                        'status'         => 'new_msg',
                    ]);

                }

                if(($check_receive_apology != null && $check_receive_congratulation == null) || ($check_receive_congratulation != null && $check_receive_apology != null && $check_receive_apology->id > $check_receive_congratulation->id)) {

                    EventUserActions::create([
                        'event_id' => $user_event->event_id,
                        'event_user_id' => $user_event->id,
                        'mobile' => $user_event->mobile,
                        'action' => 'yes_receive_apology',
                        'msg' => $request->msg,
                    ]);

                    EventMessages::create([
                        'event_id' => $user_event != null ? $user_event->event_id : 0,
                        'event_user_id' => $user_event != null ? $user_event->id : 0,
                        'name' => $user_event != null ? $user_event->name : '',
                        'mobile' => $user_event->mobile,
                        'message' => $request->msg
                    ]);

                    Notifications::create([
                        'add_by'         => 'admin',
                        'user_id'        => 1,
                        'send_to_type'   => 'user',
                        'send_to_id'     => $user_event?->event?->user_id,
                        'en_title'       => 'new apology msg to event : ' . $user_event?->event?->title,
                        'ar_title'       => 'اعتذار جديد للدعوه   : ' . $user_event?->event?->title,
                        'en_description' => 'user : ' . $user_event->name . ' send apology message : ' . $request->msg,
                        'ar_description' => 'المستخدم : ' . $user_event->name . '  أرسل الأعتذار  : ' . $request->msg,
                        'type'           => 'event-msg',
                        'item_id'        => $user_event?->event?->id,
                        'user_event_id'  => $user_event != null ? $user_event->id : 0,
                        'status'         => 'new_msg',
                      ]);

                }

            }

            return response()->json([
                'status'  => true,
                'message' => 'success',
                'data'    => null,
            ], 200);

        } else {
        	 return response()->json([
               	'status'  => false,
                'message' => 'Error',
                'data'    => null,
            ], 200);
        }



    }


    private function unique_uu_id()
    {
        $uu_id = random_int(10000, 99999);

        while (Qr_Code::where('uu_id', $uu_id)->exists()) {
            $uu_id = random_int(10000, 99999);
        }

        return $uu_id;
    }



    private function update_qr($event,$uu_id,$user_event,$image_name) {

        $color = $this->hexToRgb($event->color);

        $name_qr      = $event->name_qr;
        $number_qr    = $event->number_qr;
        $qr_height    = $event->qr_height;
        $qr_width     = $event->qr_width;
        $qr_x         = $event->qr_x;
        $qr_y         = $event->qr_y;
        $image_height = $event->image_height;
        $image_width  = $event->image_width;
        $text_color   = $event->text_color ?: '#000';

        if ($event->getRawOriginal('image') != null && file_exists(public_path('images/' . $event->getRawOriginal('image')))) {

            $image_name  = $uu_id . '-test-qr.png';
            $link        = asset('scan-qr/' . $uu_id);
            $qr_tmp_path = public_path('qr_code/tmp_' . $image_name);
            $final_path  = public_path('qr_code/' . $image_name);

            $qr_size = ($qr_width > 0 && $qr_height > 0) ? $qr_width : 300;

            QrCode::format('png')
                ->size($qr_size)
                ->color($color[0], $color[1], $color[2])
                ->backgroundColor(0, 0, 0, 0)
                ->generate($link, $qr_tmp_path);

            $background = Image::make(public_path('images/' . $event->getRawOriginal('image')));

            if ($image_width > 0 && $image_height > 0) {
                $background->resize($image_width, $image_height);
            }

            $qr = Image::make($qr_tmp_path);

            // if ($qr_width > 0 && $qr_height > 0) {
            //     $qr->resize($qr_width, $qr_height);
            // }

            // // origin: bottom-right — qr_x/qr_y = pixels from bottom-right corner
            // if ($qr_x > 0 || $qr_y > 0) {
            //     $x = $background->width()  - $qr->width()  - $qr_x;
            //     $y = $background->height() - $qr->height() - $qr_y;
            // } else {
            //     $x = intval(($background->width()  - $qr->width())  / 2);
            //     $y = intval(($background->height() - $qr->height()) / 2);
            // }

            // $background->insert($qr, 'top-left', $x, $y);
            
            if ($qr_width > 0 && $qr_height > 0) {
                $qr->resize($qr_width, $qr_height);
            }

            // تعديل نقطة البداية (Origin) لتكون Top-Left مباشرة
            if ($qr_x > 0 || $qr_y > 0) {
                $x = $qr_x; 
                $y = $qr_y;
            } else {
                $x = intval(($background->width()  - $qr->width())  / 2);
                $y = intval(($background->height() - $qr->height()) / 2);
            }

            $background->insert($qr, 'top-left', $x, $y);

            $center_x = intval($background->width() / 2);
            $text_y   = $y + $qr->height() + 15;

            if ($event->language == 'ar') {
                $Arabic    = new \ArPHP\I18N\Arabic('Glyphs');
                $font_path = public_path('font/DroidArabicKufiRegular.ttf');
                $name      = $Arabic->utf8Glyphs($user_event->name);
                $Arabic2   = new \ArPHP\I18N\Arabic('Glyphs');
                $name2     = $Arabic2->utf8Glyphs('عدد الضيوف ' . $user_event->users_count);
            } else {
                $font_path = public_path('font/LuxuriousRoman-Regular.ttf');
                $name      = $user_event->name;
                $name2     = 'Entered Users ' . $user_event->users_count;
            }

            if ($name_qr) {
                $background->text($name, $center_x, $text_y, function ($font) use ($font_path, $text_color) {
                    $font->file($font_path);
                    $font->size(20);
                    $font->color($text_color);
                    $font->align('center');
                    $font->valign('top');
                });
                $text_y += 25;
            }

            if ($number_qr && $user_event->users_count > 1) {
                $background->text($name2, $center_x, $text_y, function ($font) use ($font_path, $text_color) {
                    $font->file($font_path);
                    $font->size(20);
                    $font->color($text_color);
                    $font->align('center');
                    $font->valign('top');
                });
            }

            $background->save($final_path, 100);

            if (file_exists($qr_tmp_path)) {
                unlink($qr_tmp_path);
            }

        } else {
 
            $bg           = 'qr-image-v9.jpg';
            $link         = asset('scan-qr/' . $uu_id);
            $qr_code_path = 'qr_code/' . $image_name;

            QrCode::size(450)->format('png')->generate($link, $qr_code_path);
            Image::make($bg)->insert($qr_code_path, 'left', 320, 0)->widen(450)->save($qr_code_path, 100);

            $destination = public_path($qr_code_path);
            $new_img     = Image::make($destination);

            if ($user_event->accept_count > 1) {
                $new_img->text($user_event->accept_count, 115, 412, function ($font) {
                    $font->file(public_path('font/OpenSans-Italic.ttf'));
                    $font->size(25);
                    $font->color('#000');
                });
            }

            $new_img->save($destination);
            return $destination;
        }
    }


    private function hexToRgb(string $hex): array
    {
        $hex = str_replace('#', '', $hex);

        if (strlen($hex) === 3) {
            $r = hexdec(str_repeat(substr($hex, 0, 1), 2));
            $g = hexdec(str_repeat(substr($hex, 1, 1), 2));
            $b = hexdec(str_repeat(substr($hex, 2, 1), 2));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }

        return [$r, $g, $b];
    }
}


