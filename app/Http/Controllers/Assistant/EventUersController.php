<?php

namespace App\Http\Controllers\Assistant;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\EventUsers as Model;
use App\Models\Events;
use App\Models\EventUsers;
use App\Models\Setting;
use App\Models\EventUserLogs;
use App\Models\Qr_Code;
use App\Models\EventMessages;
use App\Models\CongratulationMessages;
use App\Models\EventFamily;
use Carbon\Carbon;
use Response;
use PDF;
use Intervention\Image\ImageManagerStatic as Image;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Notifications;


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

                return redirect()->back()->with('success', 'تم الأرسال بنجاح');
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

        if($event->image != null) {

            $bg = $event->image;
            $image_name = $uu_id . '-test-qr.png';

            $link = asset('scan-qr/' . $uu_id);
            $qr_code_path = 'qr_code/' . $image_name;

            // إنشاء QR كـ صورة مؤقتة
            QrCode::format('png')
            ->size(300)
            ->color($color[0],$color[1],$color[2])
            ->backgroundColor(0, 0, 0, 0) // RGBA => شفاف
            ->generate($link, $qr_code_path);

// افتح الخلفية
            $background = Image::make($bg);

            // افتح QR
            $qr = Image::make($qr_code_path);

            // احسب الإحداثيات لتوسيط QR في الأسفل
            $x = intval(($background->width() - $qr->width()) / 2); // مركز أفقي
            $y = $background->height() - $qr->height() - 180; // من الأسفل

            // أدرج QR
            //$background->insert($qr, 'top-left', $x, $y - 350);
            $background->insert($qr, 'top-left', $x, $y - 170);

            // احسب مركز الصورة للنص
            $center_x = intval($background->width() / 2);
            $text_y = $y + $qr->height() - 380; // أسفل QR بـ 20px

            // $Arabic = new \ArPHP\I18N\Arabic('Glyphs');
            // $name = $Arabic->utf8Glyphs($user_event->mobile);

          	/*
            $name = $user_event->mobile;

            // أضف النص في وسط الصورة أفقيًا وأسفل QR
            $background->text($name, $center_x, $text_y, function ($font) {
                $font->file(public_path('font/OpenSans.ttf'));
                $font->size(26);
                $font->color('#000');
                $font->align('center');
                $font->valign('top');
            });
            */


            // احسب مركز الصورة للنص
            $text_y2 = $y + $qr->height() + 40; // أسفل QR بـ 20px

            // $Arabic2 = new \ArPHP\I18N\Arabic('Glyphs');
            // $user_count_label = 'عدد الدخول ' . $user_event->users_count . '';
            // $name2 = $Arabic2->utf8Glyphs($user_count_label);

            if($user_event->users_count > 1) {

                $user_count_label = $user_event->users_count;
                $name2 = $user_count_label;

                // أضف النص في وسط الصورة أفقيًا وأسفل QR
                $background->text($name2, $center_x, $text_y2, function ($font) {
                    $font->file(public_path('font/OpenSans.ttf'));
                    $font->size(26);
                    $font->color('#000');
                    $font->align('center');
                    $font->valign('top');
                });

            }


            // حفظ النتيجة
            $background->save(public_path($qr_code_path), 100);

        } else {

            $bg = 'qr-image-v9.jpg';

            $link = asset('scan-qr/' . $uu_id);
            $qr_code_path = 'qr_code/' . $image_name;
            QrCode::size(450)->format('png')->generate($link, $qr_code_path);

            Image::make($bg)->insert($qr_code_path, 'left', 320, 0)->widen(450)->save($qr_code_path, 100);

            $destination = public_path($qr_code_path);

            $new_img = Image::make($destination);

          	if($user_event->users_count > 1) {
              $new_img->text($user_event->users_count, 115, 412, function ($font) {
                $font->file(public_path('font/OpenSans-Italic.ttf'));
                $font->size(25);
                $font->color('#000');
              });
            }

          	/*
            $new_img->text($user_event->mobile, 190, 680, function ($font) {
              $font->file(public_path('font/OpenSans-Italic.ttf'));
              $font->size(30);
              $font->color('#000');
              //$font->align('right'); // Adjust alignment if necessary
            });
            */

            $new_img->save($destination);

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
