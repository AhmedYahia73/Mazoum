<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests\Events as modelRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\Events as Model;
use App\Models\EventUsers;
use App\Models\EventMessages;
use App\Models\CongratulationMessages;
use App\Models\EventUserActions;
use App\Models\EventFamily;
use App\Models\MobileCodes;
use App\Models\Assistant;
use App\Models\User;
use Response;

class EventsController extends Controller
{
    private $view = 'admin.events.';
    private $redirect = 'admin/events';

    public function event_lists(){
        $employee = User::
        where("user_type", "employee")
        ->get(["id", "name"]);
        $scan_employee = User::
        where("user_type", "scan_employee")
        ->get(["id", "name"]);

        return response()->json([
            "employee" => $employee,
            "scan_employee" => $scan_employee,
        ]);
    }

    public function assistant_lists(Request $request){
        $assistants = Assistant::
        select("id", "name", "mobile")
        ->get();

        return response()->json([ 
            "assistants" => $assistants,
        ]);
    }
    
    public function users_lists(Request $request){
    
        // اللغة
        $lang = $this->get_lang();

        if (!$lang) {
            $lang = 'ar';
            app()->setLocale('ar');
            session()->put('admin_lang', 'ar');
        } else {
            app()->setLocale($lang);
        }

        // بداية الـ query
        $query = User::select(['id','name','mobile']);

        // ✔️ search
        if ($request->search) {
            $s = $request->search;

            $query->where(function($q) use ($s) {
                $q->where('name', 'like', "%$s%")
                ->orWhere('mobile', 'like', "%$s%");
            });
        }

        // ✔️ pagination
        $Item = $query->paginate(20);

        return response()->json([
            "Items" => $Item
        ]);
    }

    public function get_lang()
    {
        $lang = session()->get('admin_lang');

        if($lang == 'en' && $lang != null) {
            return $lang;
        } else {
            return 'ar';
        }
    }


    public function delete_events(Request $request) {
        $validator = Validator::make($request->all(), [
            'events' => 'required|array',
            'events.*' => 'required|exists:events,id',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }  


      	if($request->events != null && ! empty($request->events)) {

          foreach($request->events as $item) { 
              $event = Model::withTrashed()->find($item);

              if($event != null) {
                $event->delete();
              } 
          }

        }

        return response()->json([
            "success" => "You delete data success"
        ]);

    }

    public function update_event_package(Request $request,$id) {
       $validator = Validator::make($request->all(), [
            'employee_gender' => 'required',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        } 
        $Item = Model::withTrashed()->findOrFail($id);
 

        $Item->update($request->only(['employee_gender']));
        
        return response()->json([
            "success" => "You update data success"
        ]);
    }


    public function show_pdf($id)
    {

        // $Item = Model::withTrashed()->findOrFail($id);

        // $filename = 'file';

        // $path = $Item->file;

        // return Response::make(file_get_contents($path), 200, [
        //     'Content-Type' => 'application/pdf',
        //     'Content-Disposition' => 'inline; filename="'.$filename.'"'
        // ]);
        
        $Item = Model::withTrashed()->findOrFail($id);

        $filename = 'file';

        $path = $Item->file;
 
        return response()->json([
            "path" => url($path)
        ]);
    }

   public function update_location(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'lat' => 'required',
            'long' => 'required',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        } 

      $id = $request->id;
      $lat = $request->lat;
      $long = $request->long;

      $Item = Model::withTrashed()->findOrFail($id);

      $Item->update([
      	'lat' => $lat,
        'long' => $long,
        'country' => $request->country,
        'location' => $request->location,
      ]);

            
        return response()->json([
            "success" => "You update data success"
        ]);
   }

    public function index(Request $request)
    {
        // اللغة
        $lang = $this->get_lang();

        if (!$lang) {
            $lang = 'ar';
            app()->setLocale('ar');
            session()->put('admin_lang', 'ar');
        } else {
            app()->setLocale($lang);
        }

        // بداية الـ query
        $query = Model::where('country_code', 'kw')
                    ->where('is_open', 'yes')
                    ->with("user:id,name,mobile", "employee:id,name")
                    ->select([
                        'id','title','address','file','user_id',
                        'first_name','last_name','date','time', 'image',
                        'assistant_id'
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

        // ✔️ pagination
        $Item = $query->paginate(20);

        return response()->json([
            "Items" => $Item
        ]);
    }


    public function sa_events()
    { 
        $lang = $this->get_lang();

        if ($lang == null) {
            $lang = 'ar';
            app()->setLocale('ar');
            session()->put('admin_lang', 'ar');
        }

        $query = Model::where('country_code', 'sa')
            ->where('is_open', 'yes')
            ->with('user:id,name', "employee:id,name");


        // 🔎 Search
        if (request()->search) {
            $search = request()->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                ->orWhere('address', 'like', "%$search%")
                ->orWhere('first_name', 'like', "%$search%")
                ->orWhere('last_name', 'like', "%$search%");
            })
            ->orWhereHas("user", function($q) use($search){
                $q->where("name", 'like', "%$search%")
                ->orWhere("mobile", 'like', "%$search%");
            });
        }


        // 📄 Pagination (default 10)
        $Items = $query->paginate(request()->per_page ?? 10, [
            'id',
            'title',
            'address',
            'file',
            'image',
            'user_id',
            'first_name',
            'last_name',
            'date',
            'time', 
            'assistant_id'
        ]);

        return response()->json($Items);

    }

    //////////////////////////////////////////////////////


  	public function closed_events()
    {
        $lang = $this->get_lang();

        if($lang == null) {
            $lang = 'ar';
            app()->setLocale('ar');
            session()->put('admin_lang', 'ar');
        }

        $Item = Model::where('country_code','kw')
        ->with("user:id,name", "employee:id,name")->where('is_open','no')
        ->get(['id','title','address', 'image', 'file','user_id',
        'first_name' , 'last_name','date','time', 'assistant_id']);
         
        return response()->json([
            "Items" => $Item
        ]);
    }


    public function current_events()
    {
        $lang = $this->get_lang();

        if($lang == null) {
            $lang = 'ar';
            app()->setLocale('ar');
            session()->put('admin_lang', 'ar');
        }

        $Item = Model::where('country_code','kw')
        ->with("user:id,name", "employee:id,name")
        ->where('is_open','current')
        ->get(['id','title','address','file','user_id','first_name' , 
        'last_name','date','time', 'image', 'assistant_id']);
         
        return response()->json([
            "Items" => $Item
        ]);
    }

    public function deleted_events()
    {
        $lang = $this->get_lang();

        if($lang == null) {
            $lang = 'ar';
            app()->setLocale('ar');
            session()->put('admin_lang', 'ar');
        }

        $Item = Model::onlyTrashed()
        ->with("user:id,name")->where('country_code','kw')->get([
            'id','title','address','file',
            'user_id','first_name' , 'last_name',
            'date','time', 'image']);
        
        return response()->json([
            "deleted_items" => $Item
        ]);
    }

    /////////////////////////////////////////////////////////////////////////

    public function sa_closed_events()
    {
        $lang = $this->get_lang();

        if($lang == null) {
            $lang = 'ar';
            app()->setLocale('ar');
            session()->put('admin_lang', 'ar');
        }

        $Item = Model::where('country_code','sa')
        ->with("user:id,name", "employee:id,name")
        ->where('is_open','no')->get([
            'id','title','address','file',
            'user_id','first_name' , 'last_name',
            'date','time', 'image', 'assistant_id']);
        
        return response()->json([
            "closed_events" => $Item
        ]);
    }


    public function sa_current_events()
    {
        $lang = $this->get_lang();

        if($lang == null) {
            $lang = 'ar';
            app()->setLocale('ar');
            session()->put('admin_lang', 'ar');
        }

        $Item = Model::where('country_code','sa')->where('is_open','current')
        ->with("user:id,name", "employee:id,name")
        ->get(['id','title','address','file','user_id',
        'date','time', 'image', 'assistant_id']);
        
        return response()->json([
            "current_events" => $Item
        ]);
    }

    public function sa_deleted_events()
    {
        $lang = $this->get_lang();

        if($lang == null) {
            $lang = 'ar';
            app()->setLocale('ar');
            session()->put('admin_lang', 'ar');
        }

        $Item = Model::onlyTrashed()
        ->with("user:id,name")->where('country_code','sa')
        ->get([
            'id','title','address','file',
            'user_id','first_name' , 'last_name',
            'date','time', 'image']);
       
        return response()->json([
            "sa_deleted_items" => $Item
        ]);
    }


  	public function close_event($id)
    {
        $Item = Model::withTrashed()->findOrFail($id);

        $Item->update(['is_open' => 'no']);

        
        return response()->json([
            "success" => "You close event success"
        ]);

    }


    public function current_event($id)
    {
        $Item = Model::withTrashed()->findOrFail($id);

        $Item->update(['is_open' => 'current']);

        return response()->json([
            "success" => "You open event success"
        ]);

    }


  	public function un_close_event($id)
    {
        $Item = Model::withTrashed()->findOrFail($id);

        $Item->update([
            'is_open' => 'yes', 
        ]);
        $Item->restore();

        return response()->json([
            "success" => "You un close event success"
        ]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view($this->view . 'create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(modelRequest $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'sometimes|mimes:pdf,jpg,png,jpeg',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }  

        $Item = Model::withTrashed()->create($this->gteInput($request, null));

        return response()->json([
            "success" => "You add data success"
        ]);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $Item = Model::withTrashed()->findOrFail($id);

        $event_user_ids = EventUserActions::where('event_id',$Item->id)->pluck('event_user_id')->toArray();
        $event_user_ids = array_unique($event_user_ids);
        $event_users = EventUsers::whereIn('id',$event_user_ids)->get();

        if(isset(request()->event_user_id) && request()->event_user_id != null) {

            $event_user = EventUsers::findOrFail(request()->event_user_id);
            $actions = EventUserActions::where('event_id',$id)->where('event_user_id',$event_user->id)->get();

        } else {

            if($event_users != null && $event_users->count() > 0) {

                $event_user = $event_users[0];
                $actions = EventUserActions::where('event_id',$id)->where('event_user_id',$event_user->id)->get();

            } else {

                $event_user = null;
                $actions = null;
            }
        }

        return response()->json([
            'Item' => $Item,
            'event_users' => $event_users,
            'event_user' => $event_user,
            'actions' => $actions
        ]); 
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function edit($id)
    {
        $Item = Model::withTrashed()->findOrFail($id);

        $event_user_ids = EventUserActions::where('event_id',$Item->id)->pluck('event_user_id')->toArray();
        $event_user_ids = array_unique($event_user_ids);
        $event_users = EventUsers::whereIn('id',$event_user_ids)->get();

        if(isset(request()->event_user_id) && request()->event_user_id != null) {

            $event_user = EventUsers::findOrFail(request()->event_user_id);
            $actions = EventUserActions::where('event_id',$id)->where('event_user_id',$event_user->id)->get();

        } else {

            if($event_users != null && $event_users->count() > 0) {

                $event_user = $event_users[0];
                $actions = EventUserActions::where('event_id',$id)->where('event_user_id',$event_user->id)->get();

            } else {

                $event_user = null;
                $actions = null;
            }
        }

        return response()->json([
            'Item' => $Item,
            'event_users' => $event_users,
            'event_user' => $event_user,
            'actions' => $actions
        ]); 
    }


    public function event_visitors(Request $request, $id)
    {
        $Item = Model::withTrashed()->findOrFail($id);

        // Build event_users query
        $query = EventUsers::where('event_id', $id); 

        // ⭐ Search by name & mobile
        if ($request->search) {
            $s = $request->search;

            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%$s%")
                ->orWhere('mobile', 'like', "%$s%");
            });
        }

        // ⭐ Pagination
        $event_users = $query->paginate(20);
        $codes = MobileCodes::get(['id','ar_country_name','code']);

        return response()->json([
            "items" => $Item,
            "event_users" => $event_users,
            "codes" => $codes,
        ]);
    }


    public function send_events(Request $request, $id)
    {
        // Get main item (with trashed)
        $Item = Model::withTrashed()->findOrFail($id);

        // Build event_users query
        $query = EventUsers::
        where('event_id', $id)
        ->with("qr_image:id,qr,event_user_id"); 

        // ⭐ Search by name & mobile
        if ($request->search) {
            $s = $request->search;

            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%$s%")
                ->orWhere('mobile', 'like', "%$s%");
            });
        }

        // ⭐ Pagination
        $event_users = $query->paginate(20);

        return response()->json([
            "Item" => $Item,
            "event_users" => $event_users
        ]);
    }


    public function all_send_events(Request $request, $id)
    {
        // Get main item (with trashed)
        $Item = Model::withTrashed()->findOrFail($id);

        // Build event_users query
        $query = EventUsers::where('event_id', $id); 

        // ⭐ Search by name & mobile
        if ($request->search) {
            $s = $request->search;

            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%$s%")
                ->orWhere('mobile', 'like', "%$s%");
            });
        }

        // ⭐ Pagination
        $event_users = $query->get();

        return response()->json([
            "Item" => $Item,
            "event_users" => $event_users
        ]);
    }


    public function event_report($id)
    {
        $Item = Model::withTrashed()->findOrFail($id);

        $mobiles = EventUsers::
        where('event_id',$id)
        ->pluck('mobile')->toArray();
        $mobiles_arr = [];

        foreach($mobiles as $phone) {
            $mobiles_arr[] = ltrim($phone,"+");
        }
        $apologize_msgs = EventMessages::
        whereHas('event',function($event) {
            $event->whereIn('is_open',['yes','current']);
        })
        ->whereIn('mobile',$mobiles_arr)
        ->count();
        $invitees = EventUsers::
        where('event_id',$Item->id)
        ->sum('users_count');
        $qr = EventUsers::
        where('event_id',$Item->id)
        ->where('scan','yes')
        ->sum('scan_count');
        // $confirm_attend = EventUsers::
        // where('event_id',$Item->id)
        // ->where('status', 'attend')
        // ->sum('users_count');
        $new_confirm_attend = EventUsers::
        where('event_id',$Item->id)
        ->whereHas('event_action')
        ->sum('users_count');
        $confirm_attend = EventUserActions::
        where('event_id',$Item->id)
        ->where('action', 'accept_event')
        ->sum('users_count');
        $apologize = EventUsers::
        where('event_id',$Item->id)
        ->where('status','not-attend')
        ->sum('users_count');
        $waiting = EventUsers::
        where('event_id',$Item->id)
        ->where('status','hold')
        ->where('is_new_sent',0)
        ->whereNull('is_sent')
        ->sum('users_count');
        // $not_confirm = EventUsers::
        // where('event_id',$Item->id)
        // //->whereIn('status', ['sent'])
        // ->whereNull('is_accepted')
        // ->whereNull('is_refused')
        // ->where(function($query) { 
        //     $query->where('is_new_sent',1)
        //     ->orWhereNotNull('is_sent'); 
        // })
        // ->sum('users_count');
        $not_confirm = $invitees - $new_confirm_attend;
        // $send_Qr = EventUsers::
        // where('event_id',$Item->id)
        // ->where('qr_sent','yes')
        // ->sum('users_count'); 
        $send_Qr = $confirm_attend;
        $not_attend = $confirm_attend - $qr;
        $confirm_web_users = EventUserActions::
        where('event_id',$Item->id)
        ->where('action','accept_event')
        ->sum('users_count');

        $congratulation_msgs = CongratulationMessages::
        whereHas('event',function($event) { 
            $event->whereIn('is_open',['yes','current']); 
        })
        ->whereIn('mobile',$mobiles_arr)
        ->count();

        return response()->json([
            "Item" => $Item,
            "mobiles_arr" => $mobiles_arr,
            "congratulation_msgs" => $congratulation_msgs,
            "apologize_msgs" => $apologize_msgs,
            "invitees" => $invitees,
            "qr" => $qr,
            "confirm_attend" => $confirm_attend,
            "apologize" => $apologize,
            "waiting" => $waiting,
            "not_confirm" => $not_confirm,
            "send_Qr" => $send_Qr,
            "not_attend" => $not_attend,
            "confirm_web_users" => $confirm_web_users,
        ]);
    }


    public function event_users(Request $request, $id)
    {
        $Item = Model::withTrashed()->findOrFail($id);
        $user_events = EventUsers::
        where('event_id',$Item->id)
        ->where('scan','!=',null);
        
        // ⭐ Search by name & mobile
        if ($request->search) {
            $s = $request->search;

            $user_events->where(function ($q) use ($s) {
                $q->where('name', 'like', "%$s%")
                ->orWhere('mobile', 'like', "%$s%");
            });
        }

        // ⭐ Pagination
        $user_events = $user_events->paginate(20);

        return response()->json([
            "Item" => $Item,
            "user_events" => $user_events
        ]);
    }


    public function event_location($id)
    {
        $Item = Model::withTrashed()->findOrFail($id);

        return response()->json([
            "Item" => $Item
        ]);
    }


    public function enter_event($id)
    {
        $Item = Model::withTrashed()->findOrFail($id);
        $event_family = EventFamily::
        where('event_id',$id)
        ->get();
        return response()->json([
            "Item" => $Item,
            "event_family" => $event_family
        ]);
    }


    public function scanner($id)
    {
        $Item = Model::withTrashed()->findOrFail($id);

        return response()->json([
            "Item" => $Item
        ]);
    }


    public function my_package($id)
    {
        
        // "reservation_date": null,
        
        $Item = Model::
        with("user.order")
        ->withTrashed()->findOrFail($id);
        $arr = [ 
            "id"=> $Item->id,
            "title"=> $Item->title,
            "image"=> $Item->image,
            "file"=> $Item->file,
            "country"=> $Item->country,
            "country_code"=> $Item->country_code,
            "location"=> $Item->location,
            "lat"=> $Item->lat,
            "long"=> $Item->long,
            "address"=> $Item->address,
            "showing_qr"=> $Item->showing_qr,
            "date"=> $Item->date,
            "time"=> $Item->time,
            "add_by"=> $Item->add_by,
            "user_id"=> $Item->user_id,
            "user_name"=> $Item?->user?->name ?? null,
            "assistant_id"=> $Item->assistant_id,
            "gender"=> $Item->gender,
            "have_reminder"=> $Item->have_reminder,
            "can_replay_messages"=> $Item->can_replay_messages,
            "sent_remember"=> $Item->sent_remember,
            "first_name"=> $Item->first_name,
            "last_name"=> $Item->last_name,
            "is_open"=> $Item->is_open,
            "enable_resend_again"=> $Item->enable_resend_again,
            "sending_type"=> $Item->sending_type,
            "phone"=> $Item?->user?->mobile_code . $Item?->user?->mobile,
            "invitation_count"=> $Item?->user?->order?->users_count,
            "reservation_date"=> $Item?->user?->order?->start_subscription_date,
            "package_price"=> $Item?->user?->order?->total,
            "payment_type"=> $Item?->user?->order?->payment_type,
            "is_paid"=> $Item?->user?->order?->is_paid,
            "employee_gender"=> $Item?->user?->employee_gender == 'male' ? 'رجل' : 'مرأة',
            "color"=> $Item->color,
            "video"=> $Item->video,
            "created_at"=> $Item->created_at,
            "updated_at"=> $Item->updated_at,
            "deleted_at"=> $Item->id
        ];

        return response()->json([
            "Item" => $arr
        ]);
    }

    public function update_my_package(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'package_price' => 'required',
            'payment_type' => 'required|in:key_net,cash',
            'is_paid' => 'required|in:paid,not_paid',
            'invitation_count' => 'required|numeric',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        $Item = Model::
        where("id", $id)
        ->first()?->user ?? null;
        if(empty($Item)){
            return response()->json([
                "errors" => 'User not found'
            ], 400);
        }
        $order = $Item->order;
        if(empty($order)){
            return response()->json([
                "errors" => 'User is not subscriber'
            ], 400);
        }
        $order->update([
            "total" => $request->package_price,
            "payment_type" => $request->payment_type,
            "is_paid" => $request->is_paid,
            "users_count" => $request->invitation_count,
        ]);

        return response()->json([
            "Item" => $Item
        ]);
    }


    public function chat_list($id)
    {
        $Item = Model::withTrashed()->findOrFail($id);

        $event_user_ids = EventUserActions::where('event_id',$Item->id)->pluck('event_user_id')->toArray();
        $event_user_ids = array_unique($event_user_ids);
        $event_users = EventUsers::
        whereIn('id',$event_user_ids)
        ->with("qr_image:id,event_user_id,qr", "congratulation_msg:id,event_user_id,name,mobile,message")
        ->get();

        if(isset(request()->event_user_id) && request()->event_user_id != null) {

            $event_user = EventUsers::
            with("qr_image:id,event_user_id,qr", "congratulation_msg:id,event_user_id,name,mobile,message")
            ->findOrFail(request()->event_user_id);
            $actions = EventUserActions::where('event_id',$id)->where('event_user_id',$event_user->id)->get();

        } else {

            if($event_users != null && $event_users->count() > 0) {

                $event_user = $event_users[0];
                $actions = EventUserActions::where('event_id',$id)->where('event_user_id',$event_user->id)->get();

            } else {

                $event_user = null;
                $actions = null;
            }
        }

        return response()->json([
            'Item' => $Item,
            'event_users' => $event_users,
            'event_user' => $event_user,
            'actions' => $actions
        ]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(modelRequest $request, $id)
    {
        $Item = Model::withTrashed()->findOrFail($id);
        $Item->update($this->gteInput($request, $Item));

        return response()->json([
            'success' => 'You update data success', 
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $Item = Model::withTrashed()->findOrFail($id);

      	$mobiles = EventUsers::where('event_id',$Item->id)->pluck('mobile')->toArray();

      	EventMessages::whereIn('mobile',$mobiles)->delete();
        CongratulationMessages::whereIn('mobile',$mobiles)->delete();

        $Item->delete();

        // if($Item->country_code == 'kw') {
        //     return redirect($this->redirect)->with('error', trans('home.delete_msg'));
        // } else {
        //     return redirect('admin/sa-events')->with('error', trans('home.delete_msg'));
        // }
        
        return response()->json([
            'success' => 'You delete data success', 
        ]);
    }


    private function gteInput($request, $modelClass)
    {

        $input = $request->only([
            'title','lat', 'long', 'address', 'showing_qr', 'user_id' ,
            'date','time','enable_resend_again', 'assistant_id','have_reminder',
            'can_replay_messages' , 'gender' , 'sending_type' , 'color',
            'country_code', 'scan_assistant_id'
        ]);

        if(! isset($modelClass)) {
            $input['add_by'] = 'admin';
        } else {
            $input['add_by'] = $modelClass->add_by;
        }

        $path = 'images';

        if($request->file('file') != null) {

            $extension = $request->file('file')->extension();
            $filename = uniqid() . '.' . $extension;
            $request->file('file')->move($path, $filename);

            $input['file'] = $filename;

        }

        if($request->file('image') != null) {

            $extension2 = $request->file('image')->extension();
            $image_name = uniqid() . '.' . $extension2;
            $request->file('image')->move($path, $image_name);

            $input['image'] = $image_name;

        }

        if($request->file('video') != null) {

            $extension3 = $request->file('video')->extension();
            $video_name = uniqid() . '.' . $extension3;
            $request->file('video')->move($path, $video_name);

            $input['video'] = $video_name;

        }

        return  $input;
    }

    public function delete_event($id){
        $event = Model::withTrashed()
        ->find($id);
        if(empty($event)){
            return response()->json([
                "errors" => "event empty"
            ], 400);
        }
        EventUsers::
        where("event_id", $event->id)
        ->delete();
        //'','', 
        $this->delete_image($event->image);
        $this->delete_image($event->video);
        $this->delete_image($event->file);
        $event->forceDelete();
        
        return response()->json([
            "success" => "You delete event success"
        ]);
    }
    

    public function delete_image($image){
        try { 
            File::delete($imagePath); 
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
