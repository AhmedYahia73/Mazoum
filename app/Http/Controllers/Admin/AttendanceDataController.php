<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AttendanceDataController extends Controller
{
    public function index()
    {
        $items = AttendanceData::paginate(15);
        return response()->json(['items' => $items]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'router_ip'          => 'required|string',
            'locations'          => 'required|array|min:1',
            'locations.*.lat'    => 'required|numeric',
            'locations.*.lng'    => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $item = AttendanceData::create($request->only(['router_ip', 'locations']));
        return response()->json(['success' => 'You add data success', 'item' => $item]);
    }

    public function show($id)
    {
        $item = AttendanceData::findOrFail($id);
        return response()->json(['item' => $item]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'router_ip'          => 'sometimes|required|string',
            'locations'          => 'sometimes|required|array|min:1',
            'locations.*.lat'    => 'required_with:locations|numeric',
            'locations.*.lng'    => 'required_with:locations|numeric',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $item = AttendanceData::findOrFail($id);
        $item->update($request->only(['router_ip', 'locations']));
        return response()->json(['success' => 'You update data success', 'item' => $item]);
    }

    public function destroy($id)
    {
        $item = AttendanceData::findOrFail($id);
        $item->delete();
        return response()->json(['success' => 'You delete data success']);
    }

    public function multi_delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'items'   => 'required|array',
            'items.*' => 'required|exists:attendance_data,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
        AttendanceData::whereIn('id', $request->items)->delete();
        return response()->json(['success' => 'You delete data success']);
    }
}
