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
            'user_id'   => 'required|exists:users,id',
            'from_date' => 'required|date_format:Y-m-d',
            'from_time' => 'required|date_format:H:i',
            'to_date'   => 'required|date_format:Y-m-d',
            'to_time'   => 'required|date_format:H:i',
            'image'     => 'nullable|image',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $from  = $request->from_date . ' ' . $request->from_time;
        $to    = $request->to_date   . ' ' . $request->to_time;
        $image = null;

        if ($request->hasFile('image')) {
            $file  = $request->file('image');
            $name  = uniqid() . '.' . $file->extension();
            $file->move('images', $name);
            $image = $name;
        }

        $item = Attendance::create([
            'user_id'  => $request->user_id,
            'from'     => $from,
            'to'       => $to,
            'image'    => $image,
            'by_admin' => true,
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
            'from_date' => 'required|date_format:Y-m-d',
            'from_time' => 'required|date_format:H:i',
            'to_date'   => 'required|date_format:Y-m-d',
            'to_time'   => 'required|date_format:H:i',
            'image'     => 'nullable|image',
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
}
