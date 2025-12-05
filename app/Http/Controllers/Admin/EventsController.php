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
                    ->select([
                        'id','title','address','file','user_id',
                        'first_name','last_name','date','time'
                    ]);

        // ✔️ search
        if ($request->search) {
            $s = $request->search;

            $query->where(function($q) use ($s) {
                $q->where('title', 'like', "%$s%")
                ->orWhere('address', 'like', "%$s%")
                ->orWhere('first_name', 'like', "%$s%")
                ->orWhere('last_name', 'like', "%$s%");
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

        if($lang == null) {
            $lang = 'ar';
            app()->setLocale('ar');
            session()->put('admin_lang', 'ar');
        }

        $Item =  Model::where('country_code','sa')->where('is_open','yes')->get(['id','title','address','file','user_id','first_name' , 'last_name','date','time']);
        
        return response()->json([
            "Items" => $Item
        ]);
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

        $Item = Model::where('country_code','kw')->where('is_open','no')->get(['id','title','address','file','user_id','first_name' , 'last_name','date','time']);
         
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

        $Item = Model::where('country_code','kw')->where('is_open','current')->get(['id','title','address','file','user_id','first_name' , 'last_name','date','time']);
         
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

        $Item = Model::onlyTrashed()->where('country_code','kw')->get(['id','title','address','file','user_id','first_name' , 'last_name','date','time']);
        
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

        $Item = Model::where('country_code','sa')->where('is_open','no')->get(['id','title','address','file','user_id','first_name' , 'last_name','date','time']);
        
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

        $Item = Model::where('country_code','sa')->where('is_open','current')->get(['id','title','address','file','user_id','first_name' , 'last_name','date','time']);
        
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

        $Item = Model::onlyTrashed()->where('country_code','sa')->get(['id','title','address','file','user_id','first_name' , 'last_name','date','time']);
       
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

        $Item->update(['is_open' => 'yes']);

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
        $confirm_attend = EventUsers::
        where('event_id',$Item->id)
        ->where('is_accepted','yes')
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
        $not_confirm = EventUsers::
        where('event_id',$Item->id)
        ->whereIn('status', ['sent'])
        ->whereNull('is_accepted')
        ->whereNull('is_refused')
        ->where(function($query) { 
            $query->where('is_new_sent',1)->orWhereNotNull('is_sent'); 
        })
        ->sum('users_count');
        $send_Qr = EventUsers::
        where('event_id',$Item->id)
        ->where('qr_sent','yes')
        ->sum('users_count'); 
        $not_attend = EventUsers::
        where('event_id',$Item->id)
        ->where('status','attend')
        ->whereNull('scan')
        ->whereNull('is_refused')
        ->sum('users_count');
        $confirm_web_users = EventUserActions::
        where('event_id',$Item->id)
        ->where('action','accept_event')
        ->count();   

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
        $Item = Model::withTrashed()->findOrFail($id);

        return response()->json([
            "Item" => $Item
        ]);
    }


    public function chat_list($id)
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
            'country_code'
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


}
