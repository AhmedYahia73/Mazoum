<?php

namespace App\Http\Controllers\Assistant;

use App\Http\Controllers\Controller;
use App\Models\CongratulationMessages;
use App\Models\EnterUserEvent;
use App\Models\EventFamily;
use App\Models\EventMessages;
use App\Models\Events;
use App\Models\EventUserLogs;
use App\Models\EventUsers as Model;
use App\Models\EventUsers;
use App\Models\Notifications;
use App\Models\Qr_Code;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Intervention\Image\ImageManagerStatic as Image;
use PDF;
use Response;
use SimpleSoftwareIO\QrCode\Facades\QrCode;


class EventUersController extends Controller
{


    public function get_lang()
    {
        $lang = session()->get('assistant_lang');

        if($lang == 'en' && $lang != null) {
            return $lang;
        } else {
            return 'ar';
        }
    }



  	public function delete_event_users(Request $request) {

      $request->validate([
            'event_id' => 'required|exists:events,id',
            'users' => 'required',
        ]);

        $event_id = $request->event_id;

        $event = Events::where('id', $event_id)->firstOrFail();

      	if($request->users != null && ! empty($request->users)) {

          foreach($request->users as $arr) {

            if(array_key_exists('id', $arr)) {

              $user_event = Model::find($arr['id']);

              if($user_event != null) {
                $user_event->delete();
              }

            }
          }

          return redirect()->back()->with('success', 'تم الحذف بنجاح');
        }

    }


    // send_event_users
    public function send_custom_message(Request $request)
    {


        $setting = Setting::first();

        $request->validate([
            'message' => 'required',
            'event_id' => 'required|exists:events,id',
            'users' => 'required',
        ]);

        $event_id = $request->event_id;

        $event = Events::where('id', $event_id)->firstOrFail();

        try {

            $errors = 0;

            if($request->users != null && ! empty($request->users)) {

                foreach($request->users as $arr) {

                    if(array_key_exists('id', $arr) && array_key_exists('users_count', $arr)) {

                        $user_event = Model::find($arr['id']);

                        if($user_event != null) {

                            $mobile = $user_event->mobile;

                            //$to = $code.$mobile;
                            $to = $mobile;
                            $to = str_replace("+","",$to);

                            $template_name = 'custom_message';
                            $language = 'ar';

                            $message = $request->message;

                            $token          = get_whats_setting($event)['token'];
                            $sender_id      = get_whats_setting($event)['sender_id'];
                            $phone_numer_id = get_whats_setting($event)['sender_id'];

                            $response = SendTemplateV10($to,$template_name,$language,$message,$phone_numer_id,$token);

                            if ($response != null && $response->getStatusCode() == 200) {

                                $body = $response->getBody();
                                $data = json_decode($body, true);



                            } else {
                                $user_event->update([
                                    'status' => 'failed-v2',
                                ]);
                            }

                        } else {
                            $errors = $errors + 1;
                        }

                    } else {
                        $errors = $errors + 1;
                    }

                }

                return redirect()->back()->with('success', 'تم الأرسال بنجاح');
            }

        } catch(\Exception $e) {
            dd($e->getMessage(), $e->getLine());
        }

        dd('error-v2');

    }




    public function event_report($id) {

        $event = Events::where('id', $id)->firstOrFail();
        $user_events = Model::where('event_id',$id)->get();

        $data = [
            'event' => $event,
            'user_events' => $user_events
        ];

        $pdf = PDF::loadView('assistant.events.event_report', $data);

        return $pdf->stream('repoer'.$event->id.'.pdf');

        //return view('assistant.events.event_report',compact('event','user_events'));
    }


    // save_event_users
    public function save_event_users(Request $request)
    {

        $request->validate([
            'event_id' => 'required|exists:events,id',
            'event_users.*.name' => 'required',
            'event_users.*.mobile' => 'required|numeric',
          	'event_users.*.users_count' => 'required|numeric|min:1',
        ]);

        $event_id = $request->event_id;

        $event = Events::where('id', $event_id)->firstOrFail();

        if($request->event_users != null && ! empty($request->event_users)) {

            foreach ($request->event_users as $arr) {
                if($arr['name'] != null && $arr['mobile'] != null && is_numeric($arr['mobile']) && $arr['users_count'] != null && is_numeric($arr['users_count'])) {

                  $check = Model::where('event_id',$event_id)->where('mobile',ltrim($arr['mobile'],"+"))->count();

                  if($check == 0) {

                    Model::create([
                        'event_id' => $event_id,
                        'name' => $arr['name'],
                        'mobile' => ltrim($arr['mobile'],"+"),
                        'users_count' => $arr['users_count'],
                        'status' => 'hold'
                    ]);

                  }

                }
            }

        }

        return redirect()->back()->with('success', 'تم الحفظ بنجاح');

    }


    // update_event_users
    public function update_event_users(Request $request)
    {

        $request->validate([
            'old_event_users.*.name' => 'required',
            'old_event_users.*.mobile' => 'required|numeric',
            'old_event_users.*.users_count' => 'required|numeric|min:0',
        ]);

        if($request->old_event_users != null && ! empty($request->old_event_users)) {

            foreach ($request->old_event_users as $id => $arr) {

                $row = Model::find($id);

                if($row != null && $arr['name'] != null && $arr['mobile'] != null && is_numeric($arr['mobile']) && $arr['users_count'] != null && is_numeric($arr['users_count'])) {
                    $row->update([
                        'name' => $arr['name'],
                        'mobile' => ltrim($arr['mobile'],"+"),
                        'users_count' => $arr['users_count'],
                    ]);

                    // regenerate QR image if one already exists
                    $qr_record = Qr_Code::where('event_user_id', $row->id)->latest()->first();
                    if ($qr_record) {
                        $event = Events::find($row->event_id);
                        if ($event) {
                            $this->update_qr($event, $qr_record->uu_id, $row, $qr_record->qr);
                        }
                    }
                }
            }

        }

        return redirect()->back()->with('success', 'تم التحديث بنجاح');

    }



    // send_event_users
    public function send_event_users(Request $request)
    {

        $setting = Setting::first();

        $request->validate([
            'event_id' => 'required|exists:events,id',
            'users' => 'required',
            //'users.*.id' => 'required',
            //'users.*.users_count' => 'required|numeric|min:1',
        ]);

        $event_id = $request->event_id;

        $event = Events::where('id', $event_id)->firstOrFail();

        try {

            $errors = 0;

            if($request->users != null && ! empty($request->users)) {

                foreach($request->users as $arr) {

                    if(array_key_exists('id', $arr)) {

                        $user_event = Model::find($arr['id']);

                        if($user_event != null) {

                          	if(array_key_exists('users_count', $arr)) {
                                $users_count = $arr['users_count'];
                            } else {
								$users_count = $user_event->users_count;
                            }

                            $user_event->update([
                                'status' => 'hold',
                                'users_count' => $users_count,
                                'scan' => null,
                                'scan_at' => null,
                                'get_location' => null,
                              	'message_id' => null,
                              	'is_sent' => null,
                                'sent_from' => null,
                                'is_delivered' => null,
                                'is_read' => null,
                                'qr_sent' => null,
                                'is_accepted' => null,
                                'is_refused' => null,
                                'error_title' => null,
                                'error' => null,
                                'log' => null,
                            ]);

                            $image_path = $event->file;

                            //$code = $user_event->mobile_code->code;
                            //$mobile = substr($user_event->mobile, 1);
                            $mobile = $user_event->mobile;

                            //$to = $code.$mobile;
                            $to = $mobile;
                            $to = str_replace("+","",$to);

                            $template_name = 'wedding_data_v1_ar';
                            $language = 'ar';
                            $image_url = $image_path;
                            $user_name = $user_event->name;


                            $token          = get_whats_setting($event)['token'];
                            $sender_id      = get_whats_setting($event)['sender_id'];
                            $phone_numer_id = get_whats_setting($event)['sender_id'];

                            $response = SendTemplateV1($to, $template_name, $language, $image_url, $user_name, $event->title, $phone_numer_id, $token);


                            if ($response != null && $response->getStatusCode() == 200) {

                                $body = $response->getBody();
                                $data = json_decode($body, true);

                                if(array_key_exists('messages', $data) && count($data['messages']) >= 0 && array_key_exists('id', $data['messages'][0])) {
                                    $message_id = $data['messages'][0]['id'];
                                } else {
                                    $message_id = 0;
                                }

                                $user_event->update([
                                    'is_sent' => 'yes',
                                    'sent_from' => 'dashboard',
                                    'status' => 'sent',
                                    'message_id' => $message_id
                                ]);

                            } else {
                                $user_event->update([
                                    'status' => 'failed-v2',
                                ]);
                            }

                        } else {
                            $errors = $errors + 1;
                        }

                    } else {
                        $errors = $errors + 1;
                    }

                }

                return response()->json(['success', 'تم الأرسال بنجاح']);
            }

        } catch(\Exception $e) {
            dd($e->getMessage(), $e->getLine());
        }

        dd('error-v2');

    }


  	public function event_users_search(Request $request) {

        $request->validate([
          'event_id' => 'required'
        ]);

        $event_id = $request->event_id;

        $event_users = EventUsers::where('event_id',$event_id)

        ->when($request->name,function($q) use($request) {

          $q->where('name','like','%' . $request->name . '%');

        })->when($request->mobile,function($q) use($request) {

          $q->where('mobile', $request->mobile);

        })->get();

        return view('assistant.events.event_users_search', compact('event_users','event_id'));

    }


  	public function send_congratulations($id) {

      $setting = Setting::first();

      $event = Events::where('id', $id)->firstOrFail();

      $EventUsers = EventUsers::where('event_id',$id)->get();

      $arr = [];

      if($EventUsers != null && $EventUsers->count() > 0) {

        foreach($EventUsers as $user_event) {

          $mobile = $user_event->mobile;

          $to = $mobile;

          $template_name = 'wedding_data_v6_ar';
          $language = 'ar';


          $token          = get_whats_setting($event)['token'];
          $sender_id      = get_whats_setting($event)['sender_id'];
          $phone_numer_id = get_whats_setting($event)['sender_id'];

          $whatsapp = '201008478014';

          $response = SendTemplateV5($to,$template_name,$language,$whatsapp,$phone_numer_id,$token);

          if (! ($response != null && $response->getStatusCode() == 200)) {

            $arr[] = $user_event->name;

          }
        }

        if(empty($arr)) {
        	return redirect()->back()->with('success','تم ارسال التهنئه بنجاح');
        } else {
            return redirect()->back()->with('error','عفوا لم يتم ارسال تهنئه لبعض المستخدمين');
        }

      } else {
      	return redirect()->back()->with('error','عفوا لا يوجد اي مستخمدمين');
      }

    }



  	///////////////////////////////////////////////////////////////////////////////////////

  	// save_event_family
    public function save_event_family(Request $request)
    {

        $request->validate([
            'event_id' => 'required|exists:events,id',
            'event_users.*.name' => 'required',
            'event_users.*.mobile' => 'nullable|numeric',
        ]);

        $event_id = $request->event_id;

        $event = Events::where('id', $event_id)->firstOrFail();

        if($request->event_users != null && ! empty($request->event_users)) {

            foreach ($request->event_users as $arr) {
                if($arr['name'] != null) {

                  EventFamily::create([
                    'event_id' => $event_id,
                    'name' => $arr['name'],
                    'mobile' => isset($arr['mobile']) ? ltrim($arr['mobile'],"+") : null,
                    'scan_qr' => 'no'
                  ]);
                }
            }

        }

        return redirect()->back()->with('success', 'تم الحفظ بنجاح');

    }


    // update_event_family
    public function update_event_family(Request $request)
    {

        $request->validate([
            'old_event_users.*.name' => 'required',
            'old_event_users.*.mobile' => 'nullable|numeric',
        ]);

        if($request->old_event_users != null && ! empty($request->old_event_users)) {

            foreach ($request->old_event_users as $id => $arr) {

                $row = EventFamily::find($id);

                if($row != null && $arr['name'] != null) {

                  	$row->update([
                        'name' => $arr['name'],
                    	'mobile' => isset($arr['mobile']) ? ltrim($arr['mobile'],"+") : null,
                    ]);
                }
            }

        }

        return redirect()->back()->with('success', 'تم التحديث بنجاح');

    }



  	public function delete_event_family($id) {

        $user_event = EventFamily::find($id);

        if($user_event != null) {
          $user_event->delete();
        }

        return redirect()->back()->with('success', 'تم الحذف بنجاح');

    }


  	public function open_event_family($id) {

        $user_event = EventFamily::findOrFail($id);

        $user_event->update(['scan_qr' => 'yes']);

        return redirect()->back()->with('success', 'تم دخول الحفل بنجاح');

    }

  	///////////////////////////////////////////////////////////////////////////////////////

  	public function event_family_search(Request $request) {

        $request->validate([
          'event_id' => 'required'
        ]);

        $event_id = $request->event_id;

        $event_users = EventFamily::where('event_id',$event_id)

        ->when($request->name,function($q) use($request) {

          $q->where('name','like','%' . $request->name . '%');

        })->when($request->mobile,function($q) use($request) {

          $q->where('mobile', $request->mobile);

        })->get();

        return view('assistant.events.event_family_search', compact('event_users','event_id'));

    }

  	///////////////////////////////////////////////////////////////////////////////////////






    public function destroy($id)
    {
        $Item = Model::findOrFail($id);
        $Item->delete();
        return redirect()->back()->with('error', trans('home.delete_msg'));
    }


  	public function event_user_history($id)
    {
        $Item = Model::findOrFail($id);

      	$logs = EventUserLogs::where('event_user_id',$Item->id)->get();

        return view('assistant.events.event_user_history', compact('Item','logs'));
    }


  	public function send_qr($id)
    {
      	$setting = Setting::first();

        $user_event = Model::findOrFail($id);

        $event = $user_event;

        /////////////////////////////////////////////////////////////////

      	$user_event->update([ 'is_accepted' => 'yes'  ]);

        $uu_id = $this->unique_uu_id();

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

        $qr_code_path = 'qr_code/' . $image_name;

        // $bg = 'qr-image-v4.png';

        // $link = asset('scan-qr/' . $uu_id);
        // QrCode::size(900)->format('png')->generate($link, $qr_code_path);

        // Image::make($bg)->insert($qr_code_path, 'right', 60, 500)->widen(700)->save($qr_code_path, 100);

        // $destination = public_path($qr_code_path);

        // $new_img = Image::make($destination);

        // $new_img->text($user_event->users_count, 130, 645, function ($font) {
        //   $font->file(public_path('font/OpenSans-Italic.ttf'));
        //   $font->size(40);
        //   $font->color('#fff');
        // });

        // $new_img->save($destination);

        $image_url = asset($qr_code_path);

        //$code = $user_event->mobile_code->code;
        //$mobile = substr($user_event->mobile, 1);
        $mobile = $user_event->mobile;

        //$to = $code.$mobile;
        //$to = $user_event->mobile;
        $to = $mobile;

        $template_name = 'wedding_data_v2_ar';
        $language = 'ar';
        $user_name = $user_event->name;

        $token          = get_whats_setting($event)['token'];
        $sender_id      = get_whats_setting($event)['sender_id'];
        $phone_numer_id = get_whats_setting($event)['sender_id'];

        $response = SendTemplateV2($to, $template_name, $language, $image_url, $user_name, $phone_numer_id, $token);

        if ($response != null && $response->getStatusCode() == 200) {

          $user_event->update([ 'qr_sent' => 'yes'  ]);

           return redirect()->back()->with('success','تم أرسال QR Scan  بنجاح');

        } else {
        	return redirect()->back()->with('error','عفوا فشل أرسال QR Scan ');
        }

    }


    public function all_invited_users($id)
    {
        $Item = Events::findOrFail($id);

        $data = EventUsers::where('event_id',$Item->id)->get();

        $title = 'كل المدعوين';

        return view('assistant.event_details.users', compact('Item','data','title'));
    }

    public function event_qr_details($id)
    {
        $Item = Events::findOrFail($id);

        $data = EventUsers::where('event_id',$Item->id)->where('scan','yes')->get();

        $title = 'كل المدعوين الذين اكدو الحضور (QR)';

        return view('assistant.event_details.users', compact('Item','data','title'));
    }

    public function confirmed_event_details($id)
    {
        $Item = Events::findOrFail($id);

        $data = EventUsers::where('event_id',$Item->id)->where('is_accepted','yes')->get();

        $title = 'كل المدعوين الذين ينوون الحضور';

        return view('assistant.event_details.users', compact('Item','data','title'));
    }

    public function not_attend_event_details($id)
    {
        $Item = Events::findOrFail($id);

        $data = EventUsers::where('event_id',$Item->id)->where('status','not-attend')->get();

        $title = 'كل المدعوين الذين اعتذرو';

        return view('assistant.event_details.users', compact('Item','data','title'));
    }

    public function hold_event_details($id)
    {
        $Item = Events::findOrFail($id);

        $data = EventUsers::where('event_id',$Item->id)->where('status','hold')->get();

        $title = 'كل المدعوين المنتظرين';

        return view('assistant.event_details.users', compact('Item','data','title'));
    }


  	public function failed_event_details($id)
    {
        $Item = Events::findOrFail($id);

        $data = EventUsers::where('event_id',$Item->id)->where('status','failed')->get();

        $title = 'كل الدعوات التي فشلت';

      	$type = 'failed';

        return view('assistant.event_details.users', compact('Item','data','title','type'));
    }


  	public function qr_sent_event_details($id)
    {
        $Item = Events::findOrFail($id);

        $data = EventUsers::where('event_id',$Item->id)->where('qr_sent','yes')->get();

        $title = 'كل الدعوات (Sent QR)';

        return view('assistant.event_details.users', compact('Item','data','title'));
    }


  	public function congratulations_event_messages_details($id)
    {
        $Item = Events::findOrFail($id);

        $mobiles = Model::where('event_id',$Item->id)->pluck('mobile')->toArray();

        $mobiles_arr = [];

        foreach($mobiles as $phone) {
            $mobiles_arr[] = ltrim($phone,"+");
        }

        $messages = CongratulationMessages::whereIn('mobile',$mobiles_arr)->get();

        $title = 'رسائل التهنئة';

      	$type = 'congrate_message';

        return view('assistant.event_details.messages', compact('Item','messages','title','type'));
    }

    public function event_messages($id)
    {

        $Item = Events::findOrFail($id);

        $mobiles = Model::where('event_id',$Item->id)->pluck('mobile')->toArray();

        $mobiles_arr = [];

        foreach($mobiles as $phone) {
            $mobiles_arr[] = ltrim($phone,"+");
        }

        $messages = EventMessages::whereIn('mobile',$mobiles_arr)->get();

        $title = 'كل الرسائل';

      	$type = 'event_message';

        return view('assistant.event_details.messages', compact('Item','messages','title','type'));
    }


  	public function delete_event_messages($id,$type)
    {

      	if($type == 'event_message') {
             $Item = EventMessages::findOrFail($id);
        } else {
             $Item = CongratulationMessages::findOrFail($id);
        }

        $Item->delete();

        return redirect()->back()->with('error', trans('home.delete_msg'));
    }


    public function login_user(Request $request, $id) {

        $Item = Model::withTrashed()->findOrFail($id);

      	$now = Carbon::now();

        // dd($request->all());

        for($i = 1;$i <= $request->users_count;$i++) {
            $Item->update(['scan' => 'yes','scan_at' => $now,'scan_count' => $Item->scan_count + 1]);
        }

        EnterUserEvent::create([
            "event_user_id" => $id,
            "count" => $request->users_count
        ]);
        //$Item->update(['scan' => 'yes','scan_at' => $now]);

        return redirect()->back()->with('success','تم عمل QR Scan  بنجاح');
  	}

  	// public function login_user($id) {

    //     $Item = Model::findOrFail($id);

    //   	$now = Carbon::now();

    //     $Item->update(['scan' => 'yes','scan_at' => $now]);

    //     return redirect()->back()->with('success','تم عمل QR Scan  بنجاح');
  	// }


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
            make_qr_transparent(public_path($qr_code_path));
            Image::make($bg)->insert($qr_code_path, 'left', 320, 0)->widen(450)->encode('png')->save($qr_code_path);

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
        }
        dd($new_img);
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


