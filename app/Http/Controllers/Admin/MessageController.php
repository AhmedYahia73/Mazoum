<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Messages;



class MessageController extends Controller
{


    public function contact_messages()
    {
        $Item = Messages::
        orderByDesc("id")
        ->paginate(10); // عدد العناصر في كل صفحة

        return response()->json([
            'Item' => $Item,
        ]); 
    }



    public function delete_message($id)
    {

        $message = Messages::findOrFail($id);
        $message->delete();
        return response()->json([
            'success' =>  'deleted successfully', 
        ]); 
    }

     public function seen1($id)
    {

        $message = Messages::findOrFail($id);
        $message->update(['seen' => 1]);

        return response()->json([
            'success' =>  'readed successfully', 
        ]);
    }


}
