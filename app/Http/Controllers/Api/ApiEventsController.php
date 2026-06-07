<?php

namespace App\Http\Controllers\Api;

use App\Models\EventFamily;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\APiResource\UserEvents_Data;
use App\Http\Resources\APiResource\UserEventsData_V2;
use App\Http\Resources\APiResource\CongratulationMessagesResource;
use App\Http\Resources\APiResource\EventMessagesResource;
use App\Models\Events as Model;
use App\Models\EventUsers;
use App\Models\Qr_Code;
use App\Models\User;
use App\Models\EventMessages;
use App\Models\Memory;
use App\Models\CustomMemory; 
use App\Models\CongratulationMessages;

use App\Traits\GeneralTrait;
use Response;

use Illuminate\Support\Facades\Validator;

class ApiEventsController extends Controller
{
    use GeneralTrait;

    public $token;
    public $lang;

    public function __construct()
    {
        if (getallheaders() != null && ! empty(getallheaders())) {
            if (array_key_exists('language', getallheaders())) {
                $this->lang = getallheaders()['language'];
            } elseif (array_key_exists('Language', getallheaders())) {
                $this->lang = getallheaders()['Language'];
            } else {
                $this->lang = 'ar';
            }

            if (array_key_exists('token', getallheaders())) {
                $this->token = getallheaders()['token'];
            } elseif (array_key_exists('Token', getallheaders())) {
                $this->token = getallheaders()['Token'];
            } else {
                $this->token = null;
            }

        } else {
            $this->lang = null;
            $this->token = null;
        }
    }



    public function event_details($id)
    {

        if ($this->lang == null) {
            return $this->returnError('E300', 'language is required');
        }

        $lang = $this->lang;

        $user = null;

        if ($this->token != null) {
            $user = User::where('token', $this->token)->first();
        }

        if ($user == null) {
            if ($lang == 'en') {
                return $this->returnError('E100', 'user is required');
            } else {
                return $this->returnError('E100', 'المستخدم مطلوب');
            }
        }

        $Item = Model::where('id', $id)->where(function ($query) use ($user) {
              $query->where('user_id', $user->id)->orWhere('assistant_id',$user->id);
        })->select([
            'id','title', 'file as image', 'lat', 'long', 'address', 'showing_qr', 'first_name' , 'last_name' , 'date' , 'have_reminder','can_replay_messages' ,'sent_remember','sending_type',
            'resend_qr'
        ])->first();

        if ($Item != null) {

            $EventUsers = EventUsers::
            where('event_id', $Item->id)
            ->orWhere("user_id", $user->id)
            ->get(['id','name','mobile','users_count','scan_at','confirmed_at', 
            "scan_count", "accept_count", "is_sent", "is_accepted", "is_refused",
            "is_delivered", "is_read", "qr_sent", "status"])
            ->map(function($item){
                $item->scan_status = $item->users_count > $item->scan_count;
                return $item;
            });
            $user_events = UserEvents_Data::collection($EventUsers);

            $all_invited_users = EventUsers::where('event_id',$Item->id)
            ->orWhere("user_id", $user->id)
            ->get(['id','name','mobile','users_count','scan_at','confirmed_at', 
            "scan_count", "accept_count", "is_sent", "is_accepted", "is_refused",
            "is_delivered", "is_read", "qr_sent", "status"])
            ->map(function($item){
                $item->scan_status = $item->users_count > $item->scan_count;
                return $item;
            });
            //$invitations_not_sent_users = EventUsers::where('event_id',$Item->id)->where('status','hold')->get(['id','name','mobile','users_count']);
            $invitations_not_sent_users = EventUsers::
            where(function($query) use($Item, $user){
                $query->where('event_id',$Item->id)
                ->orWhere("user_id", $user->id);
            })
            ->where('status','hold')->where('is_new_sent',0)->whereNull('is_sent')
            ->get(['id','name','mobile','users_count', 
            "scan_count", "accept_count", "is_sent", "is_accepted", "is_refused",
            "is_delivered", "is_read", "qr_sent", "status"])
            ->map(function($item){
                $item->scan_status = $item->users_count > $item->scan_count;
                return $item;
            });

            //$confirmed_invitatios_users = EventUsers::where('event_id',$Item->id)->where('status','attend')->get(['id','name','mobile','users_count','scan_at','confirmed_at']);
            $confirmed_invitatios_users = EventUsers::
            where(function($query) use($Item, $user){
                $query->where('event_id',$Item->id)
                ->orWhere("user_id", $user->id);
            })
            ->where('is_accepted','yes')
            ->get(['id','name','mobile','users_count','scan_at',
            'confirmed_at', "scan_count", "accept_count", "is_sent", "is_accepted", "is_refused",
            "is_delivered", "is_read", "qr_sent", "status"])
            ->map(function($item){
                $item->scan_status = $item->users_count > $item->scan_count;
                return $item;
            });
            $send_Qr = EventUsers::
            where('event_id', $Item->id)
            ->where('qr_sent','yes')
            ->get(['id','name','mobile','users_count','scan_at',
            'confirmed_at','scan_count', "accept_count", "is_sent", "is_accepted", "is_refused",
            "is_delivered", "is_read", "qr_sent", "status"])
            ->map(function($item){
                $item->scan_status = $item->users_count > $item->scan_count;
                return $item;
            });
            $confirm_web_users = EventUsers::
            where('event_id', $Item->id)
            ->where("send_type", "link")
            ->where('qr_sent','yes')
            ->get(['id','name','mobile','users_count','scan_at','confirmed_at','scan_count', "accept_count", "is_sent", "is_accepted", "is_refused",
            "is_delivered", "is_read", "qr_sent", "status"])
            ->map(function($item){
                $item->scan_status = $item->users_count > $item->scan_count;
                return $item;
            });
            $scaned_qr_users = EventUsers::
            where(function($query) use($Item, $user){
                $query->where('event_id',$Item->id)
                ->orWhere("user_id", $user->id);
            })->where('scan','yes')
            ->get(['id','name','mobile','users_count','scan_at','confirmed_at','scan_count', "accept_count", "is_sent", "is_accepted", "is_refused",
            "is_delivered", "is_read", "qr_sent", "status"])
            ->map(function($item){
                $item->scan_status = $item->users_count > $item->scan_count;
                return $item;
            });
            $apologized_invitatios_users = EventUsers::
            where(function($query) use($Item, $user){
                $query->where('event_id',$Item->id)
                ->orWhere("user_id", $user->id);
            })
            ->where('status','not-attend')
            ->get(['id','name','mobile','users_count','scan_at','confirmed_at', "scan_count", "accept_count", "is_sent", "is_accepted", "is_refused",
            "is_delivered", "is_read", "qr_sent", "status"])
            ->map(function($item){
                $item->scan_status = $item->users_count > $item->scan_count;
                return $item;
            });

          	//$failed_invitatios_users = EventUsers::where('event_id',$Item->id)->whereIn('status',['hold','sent'])->whereNull('is_accepted')->whereNull('is_refused')->get(['id','name','mobile','users_count','scan_at','confirmed_at']);
			$failed_invitatios_users = EventUsers::
            where(function($query) use($Item, $user){
                $query->where('event_id',$Item->id)
                ->orWhere("user_id", $user->id);
            })
            ->where("accept_count", 0)
            ->where(function($query) { 
                $query->where('is_new_sent', "!=", 0)
                ->where('status', "!=", 'hold')
                ->WhereNotNull('is_sent'); 
            })
            ->get(['id', 'name', 'mobile', 'users_count', 'scan_at', 'confirmed_at', "scan_count", "accept_count", "is_sent", "is_accepted", "is_refused",
            "is_delivered", "is_read", "qr_sent", "status"])
            ->map(function($item){
                $item->scan_status = $item->users_count > $item->scan_count;
                return $item;
            });


            $enterd_events = EventFamily:: 
            where('event_id',$Item->id) 
            ->get(['id','name','mobile','scan_qr']);

            // $confirmed_without_attend = EventUsers::where('event_id',$Item->id)->where('is_accepted','yes')->where('scan','!=','yes')->get(['id','name','mobile','users_count','scan_at','confirmed_at']);

            $non_attendance_users   = EventUsers:: 
            where(function($query) use($Item, $user){
                $query->where('event_id',$Item->id)
                ->orWhere("user_id", $user->id);
            }) 
            ->get(['id','name','mobile','users_count','scan_at','confirmed_at', "scan_count", "accept_count", "is_sent", "is_accepted", "is_refused",
            "is_delivered", "is_read", "qr_sent", "status"])
            ->map(function($item){
                $attendance = $item->accept_count - $item->scan_count;
                $item->attendance = $attendance > 0 ? $attendance : 0;
                $item->scan_status = $item->users_count > $item->scan_count;
                $item->available = $attendance;
                return $item;
            });
            $non_attendance_users = collect($non_attendance_users)->where('available', '>', 0)->values();


            $arr1 = [
                'title_en' => 'all_invited_users',
                'title_ar' => '',
                'count' => $all_invited_users->sum('users_count'),
                'users' => $all_invited_users
            ];

            $arr2 = [
                'title_en' => 'invitations_not_sent_users',
                'title_ar' => '',
                'count' => $invitations_not_sent_users->sum('users_count'),
                'users' => $invitations_not_sent_users
            ];

            $arr3 = [
                'title_en' => 'confirmed_invitatios_users',
                'title_ar' => '',
              	'count' => $confirmed_invitatios_users->sum('accept_count'),
                'users' => $confirmed_invitatios_users
            ];

            $arr4 = [
                'title_en' => 'scaned_qr_users',
                'title_ar' => '',
              	'count' => $scaned_qr_users->sum('scan_count'),
                'users' => $scaned_qr_users
            ];

            $arr5 = [
                'title_en' => 'apologized_invitatios_users',
                'title_ar' => '',
                'count' => $apologized_invitatios_users->sum('users_count'),
                'users' => $apologized_invitatios_users
            ];

            $arr6 = [
                'title_en' => 'failed_invitatios_users',
                'title_ar' => '',
                'count' => $failed_invitatios_users->sum('users_count'),
                'users' => $failed_invitatios_users
            ];

            $arr7 = [
                'title_en' => 'enterd_events',
                'title_ar' => '',
                'count' => $enterd_events->count(),
                'users' => $enterd_events
            ];

            // $arr8 = [
            //     'title_en' => 'confirmed_without_attend',
            //     'title_ar' => '',
            //     'count' => $confirmed_without_attend->sum('users_count'),
            //     'users' => $confirmed_without_attend
            // ];
            $arr9 = [
                'title_en' => 'non_attendance_users',
                'title_ar' => '',
                'count' => $non_attendance_users->sum('available'),
                'users' => $non_attendance_users
            ];
            $arr10 = [
                'title_en' => 'send_Qr',
                'title_ar' => '',
                'count' => $send_Qr->sum('accept_count'),
                'users' => $send_Qr
            ];
            $arr11 = [
                'title_en' => 'confirm_web_users',
                'title_ar' => '',
                'count' => $confirm_web_users->sum('accept_count'),
                'users' => $confirm_web_users
            ];

            $event_details[] = $arr1;
            $event_details[] = $arr2;
            $event_details[] = $arr3;
            $event_details[] = $arr4;
            $event_details[] = $arr5;
            $event_details[] = $arr6;
            $event_details[] = $arr7;
            // $event_details[] = $arr8;
            $event_details[] = $arr9;
            $event_details[] = $arr10;
            $event_details[] = $arr11;

          	$mobiles = EventUsers::where('event_id',$Item->id)->pluck('mobile')->toArray();

          	$mobiles_arr = [];

          	foreach($mobiles as $phone) {
            	$mobiles_arr[] = ltrim($phone,"+");
            }

          	$event_messages = EventMessages::whereHas('event',function($event) { $event->whereIn('is_open',['yes','current']); })->whereIn('mobile',$mobiles_arr)->get(['id','name','mobile','message','created_at']);

            $event_congratulations_messages = CongratulationMessages::whereHas('event',function($event) { $event->whereIn('is_open',['yes','current']); })->whereIn('mobile',$mobiles_arr)->get(['id','name','mobile','message','created_at']);

            $data['event'] = $Item;
            $data['event_details'] = $event_details;
            $data['event_users'] = $user_events;
          	$data['event_messages'] = EventMessagesResource::collection($event_messages);

          	$data['event_congratulations_messages'] = CongratulationMessagesResource::collection($event_congratulations_messages);

            return $this->returnData('data', $data);

        } else {
            if ($lang == 'en') {
                return $this->returnError('404', 'sorry this event is not found');
            } else {
                return $this->returnError('404', 'عفوا هذا الحدث غير موجود مسبقا');
            }
        }
    }

    public function best_memories(Request $request, $id) {
        // , 
        $memories = Memory::where('event_id', $id)
        ->select(['id', 'image', 'created_at']) // تحديد الأعمدة المطلوبة
        ->paginate(10) // حدد عدد العناصر في الصفحة الواحدة (مثلاً 10)
        ->through(function($item) {
            return [
                "id" => $item->id,
                "image" => $item->image_url,
                "time" => $item->created_at->format("h:i:s A"),
            ];
        });

        return response()->json([
            "memories" => $memories,
        ]);
    }

    public function best_custom_memories(Request $request, $id) {
        // CustomMemory, 
        $memories = CustomMemory::where('custom_event_id', $id)
        ->select(['id', 'image', 'created_at']) // تحديد الأعمدة المطلوبة
        ->paginate(10) // حدد عدد العناصر في الصفحة الواحدة (مثلاً 10)
        ->through(function($item) {
            return [
                "id" => $item->id,
                "image" => $item->image_url,
                "time" => $item->created_at->format("h:i:s A"),
            ];
        });

        return response()->json([
            "memories" => $memories,
        ]);
    }

    public function index()
    {
        if ($this->lang == null) {
            return $this->returnError('E300', 'language is required');
        }

        $lang = $this->lang;

        $user = null;

        if ($this->token != null) {
            $user = User::where('token', $this->token)->first();
        }

        if ($user == null) {
            if ($lang == 'en') {
                return $this->returnError('E100', 'user is required');
            } else {
                return $this->returnError('E100', 'المستخدم مطلوب');
            }
        }

        $Item = Model::where(function ($query) use ($user) {
              $query->where('user_id', $user->id)
              ->orWhere('assistant_id',$user->id)
              ->orWhereHas("sub_user", function($query) use($user){
                $query->where("users.id", $user->id);
              });
        })->get(['id','title','address','file as image','date','time','sending_type']);

        if($Item != null && $Item->count() > 0) {
            $data = UserEventsData_V2::collection($Item);
        } else {
            $data = null;
        }

        return $this->returnData('data', $data);
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        if ($this->lang == null) {
            return $this->returnError('E300', 'language is required');
        }

        $lang = $this->lang;

        $user = null;

        if ($this->token != null) {
            $user = User::where('token', $this->token)->first();
        }

        if ($user == null) {
            if ($lang == 'en') {
                return $this->returnError('E100', 'user is required');
            } else {
                return $this->returnError('E100', 'المستخدم مطلوب');
            }
        }

        $validated_arr = [
            'title' => 'required',
            'address' => 'required',
            'file' => 'required|mimes:pdf,jpg,png,jpeg',
            'image' => 'nullable|mimes:jpg,png,jpeg',
            'video' => 'nullable',
            'pdf' => 'nullable',
            'showing_qr' => 'required',
            'lat' => 'required',
            'long' => 'required',
            'date' => 'required|date|date_format:Y-m-d',
            'time' => 'required',
            // 'sending_type' => 'required', 
            'name_qr' => 'required',
            'number_qr' => 'required',
            'qr_height' => 'required',
            'qr_width' => 'required',
            'qr_x' => 'required',
            'qr_y' => 'required',
            'resend_qr' => 'required',
        ];


        $custom_messages = [
            'title.required' => ' عنوان الحدث مطلوب ',
            'address.required' => 'موقع الحدث مطلوب',
            'showing_qr.required' => 'اظهار كود ال qr  مطلوب',

            'file.required' =>  'المرفق مطلوب',
            'file.required' =>  'المرفق مطلوب',
            'file.mimes' =>  'يجب أن يكون امتداد الصوره jpg و png و jpeg ',

            'lat.required' => ' دوائر العرض مطلوبه',
            'long.required' => ' خطوط الطول مطلوبه',

            'date.required' => 'تاريخ الحدث مطلوب',
            'time.required' => 'وقت الحدث مطلوب',
            // 'sending_type.required' => 'نوع الإرسال مطلوب', 
            'name_qr.required' => 'اسم QR مطلوب',
            'number_qr.required' => 'رقم QR مطلوب',
            'qr_height.required' => 'ارتفاع QR مطلوب',
            'qr_width.required' => 'عرض QR مطلوب',
            'qr_x.required' => 'موضع QR أفقي مطلوب',
            'qr_y.required' => 'موضع QR رأسي مطلوب',
            'resend_qr.required' => 'حقل إعادة إرسال QR مطلوب',
        ];

        if ($lang == 'ar') {
            $validator = Validator::make($request->all(), $validated_arr, $custom_messages);
        } else {
            $validator = Validator::make($request->all(), $validated_arr);
        }

        //Send failed response if request is not valid
        if ($validator->fails()) {
            $code = $this->returnCodeAccordingToInput($validator);
            return $this->returnValidationError($code, $validator);
        }

        $request->merge([
            "send_type" => "watts"
        ]);
        $arr = $this->gteInput($request, null);

        $Item = Model::create($arr);

        $arr['id'] = $Item->id;

        if ($lang == 'en') {
            return $this->returnData('data', $arr, 'event is created successfully');
        } else {
            return $this->returnData('data', $arr, 'تم أنشاء الحدث بنجاح');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        if ($this->lang == null) {
            return $this->returnError('E300', 'language is required');
        }

        $lang = $this->lang;

        $user = null;

        if ($this->token != null) {
            $user = User::where('token', $this->token)->first();
        }

        if ($user == null) {
            if ($lang == 'en') {
                return $this->returnError('E100', 'user is required');
            } else {
                return $this->returnError('E100', 'المستخدم مطلوب');
            }
        }

        $Item = Model::where('id', $id)->where('user_id', $user->id)->select([
            'id', 'title', 'file as image', 'lat', 'long', 'address', 'showing_qr','sending_type'
        ])->first();

        if ($Item != null) {
            return $this->returnData('data', $Item);
        } else {
            if ($lang == 'en') {
                return $this->returnError('404', 'sorry this event is not found');
            } else {
                return $this->returnError('404', 'عفوا هذا الحدث غير موجود مسبقا');
            }
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {

        if ($this->lang == null) {
            return $this->returnError('E300', 'language is required');
        }

        $lang = $this->lang;

        $user = null;

        if ($this->token != null) {
            $user = User::where('token', $this->token)->first();
        }

        if ($user == null) {
            if ($lang == 'en') {
                return $this->returnError('E100', 'user is required');
            } else {
                return $this->returnError('E100', 'المستخدم مطلوب');
            }
        }

        $validated_arr = [
            'event_id' => 'required',
            'title' => 'required',
            'address' => 'required',
            'file' => 'required|mimes:pdf,jpg,png,jpeg',
            'image' => 'nullable|mimes:jpg,png,jpeg',
            'video' => 'nullable',
            'pdf' => 'nullable',
            'showing_qr' => 'required',
            'lat' => 'required',
            'long' => 'required',
            'date' => 'required|date|date_format:Y-m-d',
            'time' => 'required', 
            'name_qr' => 'required',
            'number_qr' => 'required',
            'qr_height' => 'required',
            'qr_width' => 'required',
            'qr_x' => 'required',
            'qr_y' => 'required',
            'resend_qr' => 'required',
        ];

        $custom_messages = [
            'event_id.required' => 'رقم الحدث مطلوب',
            'title.required' => ' عنوان الحدث مطلوب ',
            'address.required' => 'موقع الحدث مطلوب',
            'showing_qr.required' => 'اظهار كود ال qr  مطلوب',
            'file.required' =>  'المرفق مطلوب',

            'lat.required' => ' دوائر العرض مطلوبه',
            'long.required' => ' خطوط الطول مطلوبه',

            'date.required' => 'تاريخ الحدث مطلوب',
            'time.required' => 'وقت الحدث مطلوب',
            // 'sending_type.required' => 'نوع الإرسال مطلوب', 
            'name_qr.required' => 'اسم QR مطلوب',
            'number_qr.required' => 'رقم QR مطلوب',
            'qr_height.required' => 'ارتفاع QR مطلوب',
            'qr_width.required' => 'عرض QR مطلوب',
            'qr_x.required' => 'موضع QR أفقي مطلوب',
            'qr_y.required' => 'موضع QR رأسي مطلوب',
            'resend_qr.required' => 'حقل إعادة إرسال QR مطلوب',
        ];

        if ($lang == 'ar') {
            $validator = Validator::make($request->all(), $validated_arr, $custom_messages);
        } else {
            $validator = Validator::make($request->all(), $validated_arr);
        } 
        //Send failed response if request is not valid
        if ($validator->fails()) {
            $code = $this->returnCodeAccordingToInput($validator);
            return $this->returnValidationError($code, $validator);
        }

        $Item = Model::where('id', $request->event_id)->where('user_id', $user->id)->first();

        if ($Item != null) {

            $arr = $this->gteInput($request, null);

            $Item->update($arr);

            if ($lang == 'en') {
                return $this->returnData('data', $arr, 'event is updated successfully');
            } else {
                return $this->returnData('data', $arr, 'تم تحديث الحدث بنجاح');
            }

        } else {
            if ($lang == 'en') {
                return $this->returnError('404', 'sorry this event is not found');
            } else {
                return $this->returnError('404', 'عفوا هذا الحدث غير موجود مسبقا');
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        if ($this->lang == null) {
            return $this->returnError('E300', 'language is required');
        }

        $lang = $this->lang;

        $user = null;

        if ($this->token != null) {
            $user = User::where('token', $this->token)->first();
        }

        if ($user == null) {
            if ($lang == 'en') {
                return $this->returnError('E100', 'user is required');
            } else {
                return $this->returnError('E100', 'المستخدم مطلوب');
            }
        }


        $Item = Model::where('id', $id)->where('user_id', $user->id)->first();

        if ($Item != null) {

            $Item->delete();

            if ($lang == 'en') {
                return $this->returnSuccessMessage('event is deleted successfully');
            } else {
                return $this->returnSuccessMessage('تم حذف الحدث بنجاح');
            }

        } else {
            if ($lang == 'en') {
                return $this->returnError('404', 'sorry this event is not found');
            } else {
                return $this->returnError('404', 'عفوا هذا الحدث غير موجود مسبقا');
            }
        }
    }


    private function gteInput($request, $modelClass)
    {
        $input = $request->only([
            'title','lat', 'long', 'address', 'showing_qr',
            'first_name','last_name','lat','long','date','time', 'pdf',
            'name_qr', 'number_qr', 'qr_height', 'qr_width', 'qr_x', 'qr_y',
        ]);

        if (! isset($modelClass)) {
            $input['add_by'] = 'user';
        } else {
            $input['add_by'] = $modelClass->add_by;
        }

        //////////////////////////////////////////////////
        $user = null;

        if ($this->token != null) {
            $user = User::where('token', $this->token)->first();
        }

        $user_id = $user != null ? $user->id : 0;
        $input['user_id'] = $user_id;
        //////////////////////////////////////////////////

        $path = 'images';

        if ($request->file('file') != null) {
            $extension = $request->file('file')->extension();
            $filename = uniqid() . '.' . $extension;
            $request->file('file')->move($path, $filename);

            $input['file'] = $filename;
        }

        if ($request->file('image') != null) {
            $extension2 = $request->file('image')->extension();
            $image_name = uniqid() . '.' . $extension2;
            $request->file('image')->move($path, $image_name);

            $input['image'] = $image_name;
        }

        if ($request->file('video') != null) {
            $extension3 = $request->file('video')->extension();
            $video_name = uniqid() . '.' . $extension3;
            $request->file('video')->move($path, $video_name);

            $input['video'] = $video_name;
        }

        if ($request->file('pdf') != null) {
            $extension3 = $request->file('pdf')->extension();
            $pdf_name = uniqid() . '.' . $extension3;
            $request->file('pdf')->move($path, $pdf_name);

            $input['pdf'] = $pdf_name;
        }

        return  $input;
    }

    public function qr_link(Request $request){ 
        $validator = Validator::make($request->all(), [
            "event_id" => ["required", "exists:events,id"],
            "mobile" => ["required", "numeric"],
        ]);

        $event = Model::
        where("id", $request->event_id)
        ->first();
        $event_user = EventUsers::
        where("event_id", $request->event_id)
        ->where(function($query) use($request){
            $query->where("mobile", $request->mobile)
            ->orWhereRaw("CONCAT(code, mobile) = ?", [$request->mobile]);
        })
        ->firstOrFail(); 
        $qr_link = "https://www.mazoominvitations.com/event-login/" . $event_user->code;
        $event_image = $event->image;
        $event_file = $event->file;

        return response()->json([
            "qr_link" => $qr_link,
            "event_image" => $event_image,
            "event_file" => $event_file,
        ]);
    }
}
