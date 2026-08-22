<?php

namespace App\Http\Controllers\Api\CustomEvent;

use App\Http\Controllers\Controller;
use App\Models\CustomEvent as Model;
use App\Models\CustomEventUsers;
use App\Models\CustomMessage;
use App\Models\EventVoice;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Traits\GeneralTrait;
class CustomEventController extends Controller
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
    public function index(Request $request)
    {
        $query = Model::
        where("user_id", auth()->user()->id)      
        ->orWhereHas("sub_user", function($query){
            $query->where("users.id", auth()->user()->id);
        });

        // Search
        if ($request->search) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                ->orWhere('address', 'like', "%$search%");
            });
        }

        // Pagination
        $Item = $query->paginate(15); // عدد العناصر في الصفحة

        return response()->json([
            'Item' => $Item,
        ]); 
    }

    public function data_pdf(Request $request)
    {
        $Item = Model::
        where("user_id", auth()->user()->id)      
        ->orWhereHas("sub_user", function($query){
            $query->where("users.id", auth()->user()->id);
        })
        ->get(); // عدد العناصر في الصفحة

        return response()->json([
            'Items' => $Item,
        ]); 
    }

    public function users_pdf(Request $request, $id)
    {
        $Item = CustomEventUsers::
        where("custom_event_id", $id)
        ->get(); // عدد العناصر في الصفحة

        return response()->json([
            'Items' => $Item,
        ]); 
    }

    public function template($id){
        $custom_event_user = CustomEventUsers::where("id", $id)
        ->firstOrFail();
        $event = $custom_event_user->event;
        $day_name   = Carbon::parse($event->date)->locale('ar')->translatedFormat('l');
        $invitation_text = 
                        $custom_event_user->name . "\n"
                        . "بارك الله لهما وجمع بينهم في خير .\n"
                        . "وذلك بمشيئة الله تعالى يوم " . $day_name . "\n"
                        . "الموافق " . $event->date . "\n"
                        . "وقت الاستقبال " . $event->time . " مساءًًً\n"
                        . "مكان الحفل " . $event->address . "\n"
                        . "عدد الدعوات " . $custom_event_user->users_count;

        // لطباعة النص كما هو في الـ Terminal أو داخل ملف نصي
        
        return response()->json([
            "invitation_text" => $invitation_text
        ]); 
    }
  
    // public function lists(Request $request)
    // {
    //     return view($this->view . 'create');
    // }
 
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'=> ["required"], 
            'color' => ["sometimes"], 
            'language' => ["sometimes"],
            'address' => ["required"], 
            'date' => ["required", "date", "date_format:Y-m-d"], 
            'time'=> ["required"],
            'image'   => ['sometimes'],
            'video'   => ['sometimes'],
            "name_qr" => ["required", "boolean"],
            "number_qr" => ["required", "boolean"],
            "qr_height" => ["required", "numeric"],
            "qr_width" => ["required", "numeric"],
            "qr_x" => ["required", "numeric"],
            "qr_y" => ["required", "numeric"],
            "lat" => ["required", "numeric"],
            "lng" => ["required", "numeric"],
            "send_type" => ["required", "in:all,watts,msg"],
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $request->merge([
            "user_id" => auth()->user()->id
        ]);

        Model::create($this->gteInput($request, null));
        return response()->json([
            'success' =>  'تم تخزين البيانات بنجاح', 
        ]);  
    }
    
    public function show($id)
    {
        $Item = Model::findOrFail($id);
        return response()->json([
            'Item' =>  $Item, 
        ]);  
    }
    
    public function edit($id)
    {
        $Item = Model::findOrFail($id);
        return response()->json([
            'Item' =>  $Item, 
        ]);  
    }
    
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            
            'title'=> ["required"], 
            'color' => ["sometimes"], 
            'language' => ["sometimes"],
            'address' => ["required"], 
            'date' => ["required", "date", "date_format:Y-m-d"], 
            'time'=> ["required"],
            'image'   => ['sometimes'],
            'video'   => ['sometimes'],
            "name_qr" => ["required", "boolean"],
            "number_qr" => ["required", "boolean"],
            "qr_height" => ["required", "numeric"],
            "qr_width" => ["required", "numeric"],
            "qr_x" => ["required", "numeric"],
            "qr_y" => ["required", "numeric"],
            "lat" => ["required", "numeric"],
            "lng" => ["required", "numeric"],
            "send_type" => ["required", "in:all,watts,msg"],
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        $Item = Model::
        where("user_id", auth()->user()->id)
        ->findOrFail($id);
        $Item->update($this->gteInput($request, $Item));
        return response()->json([
            'success' =>  'تم تحديث البيانات بنجاح', 
        ]); 
    }
    
    public function destroy($id)
    {
        $Item = Model::findOrFail($id);
        $Item->delete();
        return response()->json([
            'success' =>  'تم حذف البيانات بنجاح', 
        ]); 
    }


    private function gteInput($request, $modelClass)
    {
        $input = $request->only([
            'title', 'user_id', 'color' , 'assistant_id' , 'language' ,
            'address' , 'date' , 'time', 'scan_assistant_id',
            "name_qr", "number_qr", "qr_height", "send_type",
            "qr_width", "qr_x", "qr_y", "lat", "lng",
        ]);

        $path = 'images';

        if($request->file('image') != null) {

            $extension = $request->file('image')->extension();
            $filename = uniqid() . '.' . $extension;
            $request->file('image')->move($path, $filename);

            $input['image'] = $filename;
        }

        if($request->file('video') != null) {

            $extension = $request->file('video')->extension();
            $filename = uniqid() . '.' . $extension;
            $request->file('video')->move($path, $filename);

            $input['video'] = $filename;
        }

        return  $input;
    }
    
    public function custom_event_report($id)
    {
        if ($this->lang == null) {
            return $this->returnError('E300', 'language is required');
        }

        $lang = $this->lang;

        $user = null;

        if ($this->token != null) {
            $user = User::where('token', $this->token)->first();
        }
        $Item = Model::findOrFail($id);
        $visitors_count = CustomEventUsers::
        where('custom_event_id',$Item->id)
        ->where("user_id", $user?->id)
        ->sum('users_count');
        $qr_count = CustomEventUsers::
        where('custom_event_id',$Item->id)
        ->where("user_id", $user?->id)
        ->where('scan','yes')
        ->sum('scan_count');
        $event_host = User::
        where("user_id", $Item->user_id)
        ->whereHas("event_users", function($query) use($Item){
            $query->where("event_id", $Item->id);
        })
        ->count();
        $congratulation_msg = CustomMessage::
        where("custom_event_id", $Item->id)
        ->where("type", "congratulation")
        ->whereHas("user", function($query) use($user){
            $query->where("user_id", $user?->id);
        })
        ->count();
        $apologize_msg = CustomMessage::
        where("custom_event_id", $Item->id)
        ->where("type", "apologize")
        ->whereHas("user", function($query) use($user){
            $query->where("user_id", $user?->id);
        })
        ->count();
        $apologize_count = CustomEventUsers::
        where("custom_event_id", $Item->id) 
        ->where("user_id", $user?->id) 
        ->sum("apologize_count");
        $confirm_count = CustomEventUsers::
        where("custom_event_id", $Item->id) 
        ->where("user_id", $user?->id)
        ->sum("confirm_count");

        $voices = EventVoice::whereHas("custom_event_user", function($query) use($user, $id){
            $query->where("user_id", $user?->id)
                ->where("custom_event_id", $id);
        })
        ->count();

        return response()->json([
            'Item' =>  $Item, 
            'visitors_count' =>  $visitors_count, 
            'qr_count' =>  $qr_count, 
            'event_host' =>  $event_host, 
            'congratulation_msg' =>  $congratulation_msg, 
            'apologize_msg' =>  $apologize_msg, 
            'apologize_count' =>  $apologize_count, 
            'confirm_count' =>  $confirm_count, 
            'voices' =>  $voices, 
        ]); 
    }

    public function all_event_users(Request $request, $id)
    {
        if ($this->lang == null) {
            return $this->returnError('E300', 'language is required');
        }

        $lang = $this->lang;

        $user = null;

        if ($this->token != null) {
            $user = User::where('token', $this->token)->first();
        }
        $Item = Model::findOrFail($id);
        $user_events = CustomEventUsers::
        where('custom_event_id', $Item->id)
        ->when($request->search, function ($q) use ($request) {

            $search = $request->search;

            $q->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%$search%")
                    ->orWhere('mobile', 'like', "%$search%");
            });
        })
        ->where("user_id", $user?->id)
        ->get();
        $invetations = CustomEventUsers::
        where('custom_event_id',$Item->id)
        ->where("user_id", $user?->id)
        ->sum('users_count');
        $attendance = CustomEventUsers::
        where('custom_event_id',$Item->id)
        ->where("user_id", $user?->id)
        ->sum('scan_count');

        return response()->json([
            'Item' =>  $Item, 
            'user_events' =>  $user_events, 
            'invetations' =>  $invetations, 
            'attendance' =>  $attendance, 
        ]); 
    }

    public function scan_users(Request $request, $id)
    {
        if ($this->lang == null) {
            return $this->returnError('E300', 'language is required');
        }

        $lang = $this->lang;

        $user = null;

        if ($this->token != null) {
            $user = User::where('token', $this->token)->first();
        }

        $Item = Model::findOrFail($id);
        $user_events = CustomEventUsers::
        where('custom_event_id', $Item->id)
        ->where("scan_count", ">", 0)
        ->where("user_id", $user?->id)
        ->when($request->search, function ($q) use ($request) {

            $search = $request->search;

            $q->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%$search%")
                    ->orWhere('mobile', 'like', "%$search%");
            });
        })
        ->get();
        $invetations = CustomEventUsers::
        where('custom_event_id',$Item->id)
        ->where("user_id", $user?->id)
        ->sum('users_count');
        $attendance = CustomEventUsers::
        where('custom_event_id',$Item->id)
        ->where("user_id", $user?->id)
        ->sum('scan_count');

        return response()->json([
            'Item' =>  $Item, 
            'user_events' =>  $user_events, 
            'invetations' =>  $invetations, 
            'attendance' =>  $attendance, 
        ]); 
    }
    
    public function congratulation_msg($id){
        if ($this->lang == null) {
            return $this->returnError('E300', 'language is required');
        }

        $lang = $this->lang;

        $user = null;

        if ($this->token != null) {
            $user = User::where('token', $this->token)->first();
        }

        $messages = CustomMessage::
        where("custom_event_id", $id)
        ->where("type", "congratulation")
        // ->whereHas("user", function($query) use($user){
        //     $query->where("user_id", $user?->id);
        // })
        ->get()
        ->map(function($item){
            return [
                "id" => $item->id,
                "msg" => $item->msg,
                "name" => $item?->user?->name,
                "mobile" => $item?->user?->mobile,
            ];
        });

        return response()->json([
            "messages" => $messages
        ]);
    } 
    
    public function apologize_msg($id){
        // status
        if ($this->lang == null) {
            return $this->returnError('E300', 'language is required');
        }

        $lang = $this->lang;

        $user = null;

        if ($this->token != null) {
            $user = User::where('token', $this->token)->first();
        }
        $messages = CustomMessage::
        where("custom_event_id", $id)
        ->where("type", "apologize")
        // ->whereHas("user", function($query) use($user){
        //     $query->where("user_id", $user?->id);
        // })
        ->get()
        ->map(function($item){
            return [
                "id" => $item->id,
                "msg" => $item->msg,
                "name" => $item?->user?->name,
                "mobile" => $item?->user?->mobile,
            ];
        });

        return response()->json([
            "messages" => $messages
        ]);
    } 
    
    public function custom_voice_msg($id){
     // status
        if ($this->lang == null) {
            return $this->returnError('E300', 'language is required');
        }

        $lang = $this->lang;
        $user = null;

        if ($this->token != null) {
            $user = User::where('token', $this->token)->first();
        }

        $perPage = request()->get('per_page', 10); // عدد العناصر في الصفحة (افتراضياً 10)

        $messages = EventVoice::whereHas("custom_event_user", function($query) use($user, $id){
                $query->where("user_id", $user?->id)
                    ->where("custom_event_id", $id);
            })
            ->paginate($perPage)
            ->through(function($item){
                return [
                    "id" => $item->id,
                    "voice" => $item->voice,
                    "name" => $item?->custom_event_user?->name,
                    "mobile" => $item?->custom_event_user?->mobile,
                ];
            });

        return response()->json([
            "messages" => $messages
        ]);
    } 
}
