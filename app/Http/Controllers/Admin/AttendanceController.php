<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with('user:id,name,mobile');

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->search) {
            $s = $request->search;
            $query->whereHas('user', function ($q) use ($s) {
                $q->where('name', 'like', "%$s%")
                  ->orWhere('mobile', 'like', "%$s%");
            });
        }

        $items = $query->orderByDesc("id")->paginate(15);

        return response()->json(['items' => $items]);
    }

    public function user_attendance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'   => 'required|exists:users,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
        $query = Attendance::with('user:id,name,mobile')
            ->where("user_id", $request->user_id);

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->search) {
            $s = $request->search;
            $query->whereHas('user', function ($q) use ($s) {
                $q->where('name', 'like', "%$s%")
                  ->orWhere('mobile', 'like', "%$s%");
            });
        }

        $items = $query->orderByDesc("id")->paginate(15);

        return response()->json(['items' => $items]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'      => 'required|exists:users,id',
            'from_date'    => 'required|date_format:Y-m-d',
            'from_time'    => 'required|date_format:H:i',
            'to_date'      => 'required|date_format:Y-m-d',
            'to_time'      => 'required|date_format:H:i',
            'image'        => 'nullable|image',
            'second_image' => 'nullable|image',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $from  = $request->from_date . ' ' . $request->from_time;
        $to    = $request->to_date   . ' ' . $request->to_time;
        $image = null;
        $second_image = null;

        if ($request->hasFile('image')) {
            $file  = $request->file('image');
            $name  = uniqid() . '.' . $file->extension();
            $file->move('images', $name);
            $image = $name;
        } 
        if ($request->hasFile('second_image')) {
            $file  = $request->file('second_image');
            $name  = uniqid() . '.' . $file->extension();
            $file->move('images', $name);
            $second_image = $name;
        }

        $item = Attendance::create([
            'user_id'      => $request->user_id,
            'from'         => $from,
            'to'           => $to,
            'image'        => $image,
            'second_image' => $second_image,
            'by_admin'     => true,
        ]);

        return response()->json(['success' => 'You add data success', 'item' => $item->load('user:id,name,mobile')]);
    }

    public function show($id)
    {
        $item = Attendance::with('user:id,name,mobile')->findOrFail($id);
        return response()->json(['item' => $item]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'from_date'    => 'required|date_format:Y-m-d',
            'from_time'    => 'required|date_format:H:i',
            'to_date'      => 'required|date_format:Y-m-d',
            'to_time'      => 'required|date_format:H:i',
            'image'        => 'nullable|image',
            'image'        => 'nullable|image',
            'second_image' => 'nullable|image',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $item  = Attendance::findOrFail($id);
        $from  = $request->from_date . ' ' . $request->from_time;
        $to    = $request->to_date   . ' ' . $request->to_time;

        $data = ['from' => $from, 'to' => $to];

        if ($request->hasFile('image')) {
            $file  = $request->file('image');
            $name  = uniqid() . '.' . $file->extension();
            $file->move('images', $name);
            $data['image'] = $name;
        }
        if ($request->hasFile('second_image')) {
            $file  = $request->file('second_image');
            $name  = uniqid() . '.' . $file->extension();
            $file->move('images', $name);
            $data['second_image'] = $name;
        }

        $item->update($data);

        return response()->json(['success' => 'You update data success', 'item' => $item->load('user:id,name,mobile')]);
    }

    public function destroy($id)
    {
        Attendance::findOrFail($id)->delete();
        return response()->json(['success' => 'You delete data success']);
    }

    public function users_list(Request $request)
    {
        $users = User::whereIn('user_type', ['scan_employee', 'employee'])
            ->when($request->search, function ($q) use ($request) {
                $s = $request->search;
                $q->where('name', 'like', "%$s%")
                  ->orWhere('mobile', 'like', "%$s%");
            })
            ->get(['id', 'name', 'mobile', 'user_type']);

        return response()->json(['users' => $users]);
    }

    public function multi_delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'items'   => 'required|array',
            'items.*' => 'required|exists:attendance,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
        Attendance::whereIn('id', $request->items)->delete();
        return response()->json(['success' => 'You delete data success']);
    }

    public function employee_attend(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image'     => 'required|image', 
            "lat"       => "required|numeric",
            "lng"       => "required|numeric",
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        if(!$request->user()->user_type || $request->user()->user_type == "user"){
            return response()->json([
                "errors" => "يجب التسجيل كموظف"
            ]);
        }
        // التحقق من الموقع والـ IP
        $check = $this->checkLocationAndIp($request);
        if ($check !== true) {
            return response()->json(['errors' => $check], 403);
        }

        $from  = now(); 
        $image = null;

        if ($request->hasFile('image')) {
            $file  = $request->file('image');
            $name  = uniqid() . '.' . $file->extension();
            $file->move('images', $name);
            $image = $name;
        }

        $item = Attendance::create([
            'user_id'  => $request->user()->id,
            'from'     => $from, 
            'image'    => $image,
            'by_admin' => false,
        ]);

        return response()->json(['success' => 'You add data success', 'item' => $item->load('user:id,name,mobile')]);
    }

    public function employee_departure(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'second_image'  => 'required|image',
            'attendance_id' => "required|exists:attendance,id",
            "lat"           => "required|numeric",
            "lng"           => "required|numeric",
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
 
        if(!$request->user()->user_type || $request->user()->user_type == "user"){
            return response()->json([
                "errors" => "يجب التسجيل كموظف"
            ]);
        }
        // التحقق من الموقع والـ IP
        $check = $this->checkLocationAndIp($request);
        if ($check !== true) {
            return response()->json(['errors' => $check], 403);
        }

        $item = Attendance::
        where("user_id", auth()->user()->id)
        ->where("attendance_id", $request->attendance_id)
        ->whereNull("to")
        ->orderByDesc("id")
        ->first();
        if(empty($item)){
            return response()->json([
                "errors" => "يجب تسجيل الحضور أولا"
            ], 400);
        }
        $to  = now(); 
        $second_image = null;

        if ($request->hasFile('second_image')) {
            $file  = $request->file('second_image');
            $name  = uniqid() . '.' . $file->extension();
            $file->move('images', $name);
            $second_image = $name;
        }

        $item = Attendance::create([
            'user_id'      => $request->user()->id,
            'to'           => $to, 
            'second_image' => $second_image,
            'by_admin'     => false,
        ]);

        return response()->json(['success' => 'You add data success', 'item' => $item->load('user:id,name,mobile')]);
    }

    /**
     * التحقق من أن الموقع داخل الحدود والـ IP صح
     * returns true if valid, or error string if not
     */
    private function checkLocationAndIp(Request $request)
    {
        $allOffices = \App\Models\AttendanceData::all();

        if ($allOffices->isEmpty()) {
            return true; // لو مفيش إعدادات نسمح بالتسجيل
        }

        // تحضير الـ location
        $location = $request->location;  
        $lat = (float) $request->lat;
        $lng = (float) $request->lng; 

        $clientIp = $request->ip();

        // يكفي مكتب واحد يطابق الشرطين
        foreach ($allOffices as $office) {

            $ipOk = !$office->router_ip || $clientIp === $office->router_ip;

            $locationOk = empty($office->locations) || count($office->locations) < 3
                || $this->isInsidePolygon($lat, $lng, $office->locations);

            if ($ipOk && $locationOk) {
                return true;
            }
        }

        return 'أنت خارج نطاق العمل المسموح به أو غير متصل بشبكة أي مكتب';
    }

    /**
     * Ray casting algorithm — هل النقطة داخل الـ polygon؟
     */
    private function isInsidePolygon(float $lat, float $lng, array $polygon): bool
    {
        $count   = count($polygon);
        $inside  = false;
        $j       = $count - 1;

        for ($i = 0; $i < $count; $i++) {
            $xi = (float) $polygon[$i]['lat'];
            $yi = (float) $polygon[$i]['lng'];
            $xj = (float) $polygon[$j]['lat'];
            $yj = (float) $polygon[$j]['lng'];

            if ((($yi > $lng) !== ($yj > $lng)) &&
                ($lat < ($xj - $xi) * ($lng - $yi) / ($yj - $yi) + $xi)) {
                $inside = !$inside;
            }
            $j = $i;
        }

        return $inside;
    }
}