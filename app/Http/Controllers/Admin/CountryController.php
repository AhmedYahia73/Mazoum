<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country as Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CountryController extends Controller
{
    public function index(Request $request)
    {
        $query = Model::query();

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $items = $query->orderByDesc('id')->paginate(15);

        return response()->json(['items' => $items]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'   => 'required|string|max:255|unique:countries,name',
            'status' => 'required|boolean',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $item = Model::create([
            'name'   => $request->name,
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
            'name'   => 'required|string|max:255|unique:countries,name,' . $id,
            'status' => 'required|boolean',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $item = Model::findOrFail($id);
        $item->update([
            'name'   => $request->name,
            'status' => $request->status,
        ]);

        return response()->json(['success' => 'You update data success', 'item' => $item]);
    }

    public function destroy($id)
    {
        $item = Model::
        whereDoesntHave('events')
        ->where("id", $id)->first();
        if (!$item) {
            return response()->json(['error' => 'You can not delete this item because it has related events'], 400);
        }
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
        ->whereDoesntHave('events')
        ->delete();
        return response()->json(['success' => 'You delete data success']);
    }
}
