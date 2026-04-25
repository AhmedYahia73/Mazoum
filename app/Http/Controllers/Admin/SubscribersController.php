<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Subscribers;
use Illuminate\Support\Facades\Validator;



class SubscribersController extends Controller
{


    public function subscribers(Request $request)
    {
        $query = Subscribers::query();
        // Search
        if ($request->search) {
            $query->where('email', 'like', '%' . $request->search . '%');
        }

        // Pagination
        $Item = $query->paginate(10); // عدد النتائج في الصفحة

        return response()->json([
            'Item' => $Item,
        ]);
    }


    public function delete_subscriber(Request $request , $id)
    {
        $subscriber = Subscribers::findOrFail($id);
        $subscriber->delete();
        return response()->json([
            'success' =>  'deleted successfully', 
        ]); 
    }

    public function multi_delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'items'   => 'required|array',
            'items.*' => 'required|exists:subscribers,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
        Subscribers::whereIn('id', $request->items)->delete();
        return response()->json(['success' => 'deleted successfully']);
    }


    public function seen($id)
    {

        $message = Subscribers::findOrFail($id);
        $message->update(['seen' => 1]);
        return response()->json([
            'success' =>  'seen successfully', 
        ]); 
    }

}
