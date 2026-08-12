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
              $query->where('user_id', $user->id)
              ->orWhereHas("sub_user", function($q) use($user){
                $q->where("users.id", $user->id);
              })
              ->orWhere('assistant_id',$user->id);
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

            $scan_enterd_events = EventFamily:: 
            where('event_id',$Item->id)
            ->where('scan_qr','yes')
            ->get(['id','name','mobile','scan_qr']);
            $not_scan_enterd_events = EventFamily:: 
            where('event_id',$Item->id)
            ->where('scan_qr','no')
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
             $arr12 = [
                'title_en' => 'scan_enterd_events',
                'title_ar' => '',
                'count' => $scan_enterd_events->count(),
                'users' => $scan_enterd_events
            ];
             $arr13 = [
                'title_en' => 'not_scan_enterd_events',
                'title_ar' => '',
                'count' => $not_scan_enterd_events->count(),
                'users' => $not_scan_enterd_events
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
            $event_details[] = $arr12;
            $event_details[] = $arr13;

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
        $memories = Memory::where('event_id', $id)  // تحديد الأعمدة المطلوبة
        ->with("user:id,name,mobile")
        ->paginate(10) // حدد عدد العناصر في الصفحة الواحدة (مثلاً 10)
        ->through(function($item) {
            return [
                "id" => $item->id,
                "image" => $item->image_url,
                "time" => $item?->created_at?->format("h:i:s A"),
                "user" => $item->user,
            ];
        });

        return response()->json([
            "memories" => $memories,
        ]);
    }

    public function best_custom_memories(Request $request, $id) {
        // CustomMemory, 
        $memories = CustomMemory::where('custom_event_id', $id)  // تحديد الأعمدة المطلوبة
        ->with("user:id,name,mobile")
        ->paginate(10) // حدد عدد العناصر في الصفحة الواحدة (مثلاً 10)
        ->through(function($item) {
            return [
                "id" => $item->id,
                "image" => $item->image_url,
                "time" => $item?->created_at?->format("h:i:s A"),
                "user" => $item->user,
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
        })->get(['id','title','address','file as image','date','time',
        'sending_type']);

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

        $Item = Model::where('id', $id)->first();

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
        $qr_link = "https://www.mazoominvitations.com/event-login/" . $event_user->code . "?type=video";
        $event_image = $event->image;
        $event_file = $event->file;

        return response()->json([
            "qr_link" => $qr_link,
            "event_image" => $event_image,
            "event_file" => $event_file,
        ]);
    }

    /**
     * GET /api/user/event-users/{id}/{type}
     * type: all_invited_users | invitations_not_sent_users | confirmed_invitatios_users
     *       scaned_qr_users | apologized_invitatios_users | failed_invitatios_users
     *       enterd_events | non_attendance_users | send_Qr | confirm_web_users
     *       scan_enterd_events | not_scan_enterd_events
     */

    public function event_users_list(Request $request, $id, $type)
    {
        if ($this->token == null) {
            return $this->returnError('E100', 'المستخدم مطلوب');
        }

        $user = User::where('token', $this->token)->first();
        if (!$user) {
            return $this->returnError('E100', 'المستخدم مطلوب');
        }

        $Item = Model::where('id', $id)->where(function ($q) use ($user) {
            $q->where('user_id', $user->id)
            ->orWhereHas("sub_user", function($q) use($user){
                $q->where("users.id", $user->id);
              })
              ->orWhere('assistant_id', $user->id);
        })->first();

        if (!$Item) {
            return $this->returnError('404', 'عفوا هذا الحدث غير موجود');
        }

        $user_status = $Item->user_id == $user->id;
        $user_id = $user->id;
        $search = $request->search;
        $perPage = $request->per_page ?? 15;

        $baseFields = ['id','name','mobile','users_count','scan_at','confirmed_at',
            'scan_count','accept_count','is_sent','is_accepted','is_refused',
            'is_delivered','is_read','qr_sent','status', 'suit_num'];

        switch ($type) {

            case 'all_invited_users':
                $query = EventUsers::where('event_id', $Item->id)
                ->where(function($q) use($user){
                    $q->where("user_id", $user->id)
                    ->orWhereNull("user_id");
                });
                break;

            case 'invitations_not_sent_users':
                $query = EventUsers::where('event_id', $Item->id)
                    ->where('status', 'hold')
                    ->where('is_new_sent', 0)
                    ->whereNull('is_sent')
                    ->where(function($q) use($user){
                        $q->where("user_id", $user->id)
                        ->orWhereNull("user_id");
                    });
                break;

            case 'confirmed_invitatios_users':
                $query = EventUsers::where('event_id', $Item->id)
                    ->where(function($q) use($user){
                        $q->where("user_id", $user->id)
                        ->orWhereNull("user_id");
                    });
                break;

            case 'scaned_qr_users':
                $query = EventUsers::where('event_id', $Item->id)
                    ->where('scan', 'yes')->where(function($q) use($user){
                        $q->where("user_id", $user->id)
                        ->orWhereNull("user_id");
                    });
                break;

            case 'apologized_invitatios_users':
                $query = EventUsers::where('event_id', $Item->id)
                    ->where('status', 'not-attend')->where(function($q) use($user){
                        $q->where("user_id", $user->id)
                        ->orWhereNull("user_id");
                    });
                break;

            case 'failed_invitatios_users':
                $query = EventUsers::where('event_id', $Item->id)
                    ->where('accept_count', 0)
                    ->where(function ($q) {
                        $q->where('is_new_sent', '!=', 0)
                          ->where('status', '!=', 'hold')
                          ->whereNotNull('is_sent');
                    })->where(function($q) use($user){
                        $q->where("user_id", $user->id)
                        ->orWhereNull("user_id");
                    });
                break;

            case 'send_Qr':
                $query = EventUsers::where('event_id', $Item->id)
                    ->where('qr_sent', 'yes')->where(function($q) use($user){
                        $q->where("user_id", $user->id)
                        ->orWhereNull("user_id");
                    });
                break;

            case 'confirm_web_users':
                $query = EventUsers::where('event_id', $Item->id)
                    ->where('send_type', 'link')
                    ->where('qr_sent', 'yes')->where(function($q) use($user){
                        $q->where("user_id", $user->id)
                        ->orWhereNull("user_id");
                    });
                break;

            case 'remember_users':
                $query = EventUsers::where('event_id', $Item->id)
                    ->where('remember', 1);
                break;

            case 'non_attendance_users':
                $data = EventUsers::where('event_id', $Item->id)
                    ->when($search, fn($q) => $q->where('name', 'like', "%$search%")->orWhere('mobile', 'like', "%$search%"))
                    ->where(function($q) use($user){
                        $q->where("user_id", $user->id)
                        ->orWhereNull("user_id");
                    })
                    ->get($baseFields)
                    ->map(function ($item) {
                        $attendance = $item->accept_count - $item->scan_count;
                        $item->available = $attendance > 0 ? $attendance : 0;
                        $item->scan_status = $item->users_count > $item->scan_count;
                        return $item;
                    })
                    ->where('available', '>', 0)
                    ->values();

                $page    = $request->page ?? 1;
                $offset  = ($page - 1) * $perPage;
                $total   = $data->count();
                $paged   = $data->slice($offset, $perPage)->values();

                return $this->returnData('data', [
                    'title_en' => $type,
                    'count'    => $data->sum('available'),
                    'users'    => [
                        'data'         => $paged,
                        'current_page' => (int) $page,
                        'per_page'     => (int) $perPage,
                        'total'        => $total,
                        'last_page'    => (int) ceil($total / $perPage),
                    ],
                ]);

            case 'enterd_events':
                
                $familyQuery = EventFamily::where('event_id', $Item->id)
                    ->when($search, fn($q) => $q->where('name', 'like', "%$search%")->orWhere('mobile', 'like', "%$search%"));
                $paged = $familyQuery->paginate($perPage);
                return $this->returnData('data', [
                    'title_en' => $type,
                    'count'    => EventFamily::where('event_id', $Item->id)->count(),
                    'users'    => $paged,
                ]);

            case 'scan_enterd_events':
                $familyQuery = EventFamily::where('event_id', $Item->id)->where('scan_qr', 'yes')
                    ->when($search, fn($q) => $q->where('name', 'like', "%$search%")->orWhere('mobile', 'like', "%$search%"));
                $paged = $familyQuery->paginate($perPage);
                return $this->returnData('data', [
                    'title_en' => $type,
                    'count'    => EventFamily::where('event_id', $Item->id)->where('scan_qr', 'yes')->count(),
                    'users'    => $paged,
                ]);

            case 'not_scan_enterd_events':
                $familyQuery = EventFamily::where('event_id', $Item->id)->where('scan_qr', 'no')
                    ->when($search, fn($q) => $q->where('name', 'like', "%$search%")->orWhere('mobile', 'like', "%$search%"));
                $paged = $familyQuery->paginate($perPage);
                return $this->returnData('data', [
                    'title_en' => $type,
                    'count'    => EventFamily::where('event_id', $Item->id)->where('scan_qr', 'no')->count(),
                    'users'    => $paged,
                ]);

            default:
                return $this->returnError('E400', 'نوع غير صحيح');
        }

        // EventUsers based types
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('mobile', 'like', "%$search%");
            });
        }

        // count حسب كل type
        $countCol = match($type) {
            'confirmed_invitatios_users' => 'accept_count',
            'scaned_qr_users'            => 'scan_count',
            'send_Qr'                    => 'accept_count',
            'confirm_web_users'          => 'accept_count',
            default                      => 'users_count',
        };

        if($type == "all_invited_users" ||$type == "invitations_not_sent_users" ||
            $type == "confirmed_invitatios_users" ||$type == "scaned_qr_users" ||
            $type == "apologized_invitatios_users" ||$type == "failed_invitatios_users" ||
            $type == "send_Qr" ||$type == "confirm_web_users" || $type == "non_attendance_users" ||
            $type == "remember_users"){
            $query = !$user_status ? $query->where("user_id", $user_id): 
            $query->where(function($query) use($user_id){
                $query->whereNull("user_id")
                ->orWhere("user_id", $user_id);
            });
        }
        $count = (clone $query)->sum($countCol);
        $paged = $query->paginate($perPage, $baseFields);

        return $this->returnData('data', [
            'title_en' => $type,
            'count'    => $count,
            'users'    => $paged,
        ]);
    }
    
    public function event_users_count(Request $request, $id)
    {
        if ($this->token == null) {
            return $this->returnError('E100', 'المستخدم مطلوب');
        }

        $user = User::where('token', $this->token)->first();
        if (!$user) {
            return $this->returnError('E100', 'المستخدم مطلوب');
        }
 
        $Item = Model::where('id', $id)->where(function ($query) use ($user) {
              $query->where('user_id', $user->id)   
              ->orWhereHas("sub_user", function($q) use($user){
                $q->where("users.id", $user->id);
              })->orWhere('assistant_id',$user->id);
        })->first();

        if (!$Item) {
            return $this->returnError('404', 'عفوا هذا الحدث غير موجود');
        } 
 
        $user_status = $Item->user_id == $user->id;
        $user_id = $user->id; 
        $all_invited_users = EventUsers::where('event_id', $Item->id);
            $all_invited_users = !$user_status ? $all_invited_users->where("user_id", $user_id)->sum('users_count'): 
            $all_invited_users->where(function($query) use($user_id){
                $query->whereNull("user_id")
                ->orWhere("user_id", $user_id);
            })
            ->sum("users_count");
        $remember_users = EventUsers::where('event_id', $Item->id)
        ->where("remember", 1);
            $remember_users = !$user_status ? $remember_users->where("user_id", $user_id)->sum('users_count'): 
            $remember_users->where(function($query) use($user_id){
                $query->whereNull("user_id")
                ->orWhere("user_id", $user_id);
            })
            ->sum("users_count");
        $invitations_not_sent_users = EventUsers::where('event_id', $Item->id);
                
            $invitations_not_sent_users = !$user_status ? $invitations_not_sent_users->where("user_id", $user_id)->sum('users_count'): 
            $invitations_not_sent_users->where(function($query) use($user_id){
                $query->whereNull("user_id")
                ->orWhere("user_id", $user_id);
            })
            ->where('status', 'hold')
            ->where('is_new_sent', 0)
            ->whereNull('is_sent')
            ->sum('users_count');
        $confirmed_invitatios_users = EventUsers::
            where('event_id', $Item->id);
            $confirmed_invitatios_users = !$user_status ? $confirmed_invitatios_users->where("user_id", $user_id)->sum('accept_count'): 
            $confirmed_invitatios_users->where(function($query) use($user_id){
                $query->whereNull("user_id")
                ->orWhere("user_id", $user_id);
            })
            ->sum('accept_count'); 

        $scaned_qr_users = EventUsers::
            where('event_id',$Item->id)
            ->where('scan','yes');
            $scaned_qr_users = !$user_status ? $scaned_qr_users->where("user_id", $user_id)->sum('scan_count'): 
            $scaned_qr_users->where(function($query) use($user_id){
                $query->whereNull("user_id")
                ->orWhere("user_id", $user_id);
            })
            ->sum('scan_count');
        $apologized_invitatios_users = EventUsers::
        where('event_id',$Item->id)
        ->where('status','not-attend');
            $apologized_invitatios_users = !$user_status ? $apologized_invitatios_users->where("user_id", $user_id)->sum('users_count'): 
            $apologized_invitatios_users->where(function($query) use($user_id){
                $query->whereNull("user_id")
                ->orWhere("user_id", $user_id);
            })
        ->sum('users_count'); 
        $failed_invitatios_users = EventUsers::where('event_id', $Item->id)
            ->where('accept_count', 0)
            ->where('status', "!=", 'not-attend')
            ->where(function($query) { 
                $query->where('is_new_sent', "!=", 0)
                ->orWhere('status', "!=", 'hold')
                ->orWhereNotNull('is_sent'); 
            });
            $failed_invitatios_users = !$user_status ? $failed_invitatios_users->where("user_id", $user_id)->sum('users_count'): 
            $failed_invitatios_users->where(function($query) use($user_id){
                $query->whereNull("user_id")
                ->orWhere("user_id", $user_id);
            })
            ->sum('users_count');
        $send_Qr = EventUsers::where('event_id', $Item->id)
            ->where('qr_sent', 'yes');
            $send_Qr = !$user_status ? $send_Qr->where("user_id", $user_id)->sum('accept_count'): 
            $send_Qr->where(function($query) use($user_id){
                $query->whereNull("user_id")
                ->orWhere("user_id", $user_id);
            })
            ->sum('accept_count'); 
        $confirm_web_users = EventUsers::where('event_id', $Item->id)
            ->where('send_type', 'link')
            ->where('qr_sent', 'yes');
            $confirm_web_users = !$user_status ? $confirm_web_users->where("user_id", $user_id)->sum('accept_count'): 
            $confirm_web_users->where(function($query) use($user_id){
                $query->whereNull("user_id")
                ->orWhere("user_id", $user_id);
            })
            ->sum('accept_count'); 
        $non_attendance_users = $confirmed_invitatios_users - $scaned_qr_users;
        $enterd_events = EventFamily::where('event_id', $Item->id)
        ->count(); 
        $scan_enterd_events = EventFamily::where('event_id', $Item->id)
        ->where('scan_qr', 'yes')
        ->count(); 
        $not_scan_enterd_events = EventFamily::where('event_id', $Item->id)
        ->where('scan_qr', 'no')
        ->count();
        $congratulation_msgs = CongratulationMessages::
        where("event_id", $Item->id)
        ->count();
        $apologize_msgs = EventMessages::
        where("event_id", $Item->id)
        ->count();

        return $this->returnData('data', [ 
            'Item' => $Item,
            "all_invited_users" => intval($all_invited_users), 
            "invitations_not_sent_users" => intval($invitations_not_sent_users), 
            "confirmed_invitatios_users" => intval($confirmed_invitatios_users),
            "scaned_qr_users" => intval($scaned_qr_users), 
            "apologized_invitatios_users" => intval($apologized_invitatios_users), 
            "failed_invitatios_users" => intval($failed_invitatios_users), 
            "send_Qr" => intval($send_Qr), 
            "confirm_web_users" => intval($confirm_web_users), 
            "non_attendance_users" => intval($non_attendance_users), 
            "enterd_events" => intval($enterd_events),
            "scan_enterd_events" => intval($scan_enterd_events), 
            "not_scan_enterd_events" => intval($not_scan_enterd_events),
            "congratulation_msgs" => intval($congratulation_msgs),
            "apologize_msgs" => intval($apologize_msgs),
            "remember_users" => intval($remember_users),
        ]);

    } 

    public function apologize_msgs(Request $request, $id){
         
        $apologize_msgs = EventMessages::
        where("event_id", $id)
        ->whereNull("message_id")
        ->when($request->search, function ($q) use ($request) {
            $search = $request->search;
            $q->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%$search%")
                    ->orWhere('mobile', 'like', "%$search%");
            });
        })
        ->with("reply:id,name,mobile,message,type,message_id")
        ->paginate(15); 

        return response()->json([
            "apologize_msgs" => $apologize_msgs
        ]);
    }

    public function congratulation_msgs(Request $request, $id){
        
        $congratulation_msgs = CongratulationMessages::
        where("event_id", $id)
        ->whereNull("message_id")
        ->when($request->search, function ($q) use ($request) {
            $search = $request->search;
            $q->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%$search%")
                    ->orWhere('mobile', 'like', "%$search%");
            });
        })
        ->with("reply:id,name,mobile,message,type,message_id")
        ->paginate(15); 

        return response()->json([
            "congratulation_msgs" => $congratulation_msgs
        ]);
    }

    public function all_events(Request $request)
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
        
        $query = Model::
        where(function ($query) use ($user) {
            $query->where('user_id', $user->id)
            ->orWhere('assistant_id',$user->id)
            ->orWhereHas("sub_user", function($query) use($user){
                $query->where("users.id", $user->id);
            });
        })
        ->where('is_open', 'yes')
        ->with("user:id,name,mobile", "employee:id,name")
        ->select([
            'id','title','address','file','user_id',
            'first_name','last_name','date','time', 'image',
            'assistant_id', 'is_open'
        ]); 
        // ✔️ search
        if ($request->search) {
            $s = $request->search;

            $query->where(function($q) use ($s) {
                $q->where('title', 'like', "%$s%")
                ->orWhere('address', 'like', "%$s%")
                ->orWhere('first_name', 'like', "%$s%")
                ->orWhere('last_name', 'like', "%$s%")
                ->orWhereHas("user", function($q2) use($s){
                    $q2->where("name", "like", "%$s%")
                    ->orWhere('mobile', 'like', "%$s%");
                });
            });
        } 

        $data = $query->paginate(20);
 
        return $this->returnData('data', $data);
    }

    public function best_memories_users(Request $request, $id)
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

        $users = EventUsers::
        where("event_id", $id)
        ->whereHas("best_memories")
        ->paginate(15);

        return $this->returnData('users', $users);

    }
}
