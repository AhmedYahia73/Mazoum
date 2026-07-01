<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NewSetting as Model;
use App\Models\Country;
use Illuminate\Support\Facades\Validator;

class PhoneSettingController extends Controller
{
    public function index(Request $request)
    {
        $items = Model::
        with("country:id,name")
        ->get();

        return response()->json(['items' => $items]);
    }

    public function lists(Request $request)
    {
        $items = Country::
        select('id', 'name')
        ->get();

        return response()->json(['items' => $items]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_numer_id'   => 'required',
            'sender_id'   => 'required',
            'country_id' => 'required|exists:countries,id',
            'status' => 'required|boolean',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $item = Model::create([
            'phone_numer_id' => $request->phone_numer_id,
            'sender_id' => $request->sender_id,
            'country_id' => $request->country_id,
            'status' => $request->status,
        ]);

        return response()->json(['success' => 'You add data success', 'item' => $item]);
    }

    public function show($id)
    {
        $item = Model::findOrFail($id);
        return response()->json(['item' => $item]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'phone_numer_id'   => 'required',
            'sender_id'   => 'required',
            'country_id' => 'required|exists:countries,id',
            'status' => 'required|boolean',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $item = Model::findOrFail($id);
        $item->update([
            'phone_numer_id' => $request->phone_numer_id,
            'sender_id' => $request->sender_id,
            'country_id' => $request->country_id,
            'status' => $request->status,
        ]);

        return response()->json(['success' => 'You update data success', 'item' => $item]);
    }

    public function destroy($id)
    {
        $item = Model::
        where("id", $id)
        ->firstOrFail();
        $item->delete();
        return response()->json(['success' => 'You delete data success']);
    }

    public function multi_delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'items'   => 'required|array',
            'items.*' => 'required|exists:countries,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        Model::whereIn('id', $request->items)
        ->delete();
        return response()->json(['success' => 'You delete data success']);
    }

    public function change_status($id)
    {
        $item = Model::findOrFail($id);
        $item->update([
            'status' => $item->status ? false : true,
        ]);
        return response()->json(['success' => 'You update data success', 'item' => $item]);
    }
}
