<?php

namespace App\Http\Controllers\Api\CustomEvent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CustomEvent as Model;
use App\Models\CustomEventUsers; 
use App\Models\EventUsers;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class CustomEventController extends Controller
{
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
}
