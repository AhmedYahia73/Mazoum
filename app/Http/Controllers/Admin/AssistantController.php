<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests\Assistant as modelRequest;
use App\Http\Controllers\Controller;
use App\Models\User as Model;
use App\Models\Assistant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AssistantController extends Controller
{  
    public function index()
    {
        $Item = Model::
        WhereIn("user_type", ["scan_employee", "employee"])->get([
            'id', 'name', 'mobile', 'email', 'status',
            'employee_gender', 'mobile_code', 'user_type',
            'salary', 'appointment_from', 'appointment_to',
            "holiday", "negotation_status",
        ]);
        return response()->json([
            'assistants' => $Item
        ]);
    }

 
    public function create()
    {
        return view('admin.assistant.create');
    }
 
    public function store(modelRequest $request)
    {
        if($request->user_type == "employee"){   
            $validator = Validator::make($request->all(), [
                'negotation_status' => 'required|boolean',
            ]); 
            if ($validator->fails()) { // if Validate Make Error Return Message Error
                return response()->json([
                    'errors' => $validator->errors(),
                ],400);
            }   
        }
        Model::create($this->gteInput($request,null));
        
        return response()->json([
            'success' => 'You add data success'
        ]);
    }
 
    public function show($id)
    {
        $Item = Model::
        select('id', 'name', 'mobile', 'email', 'status',
        'employee_gender', 'mobile_code', "user_type", "holiday",
        'salary', 'appointment_from', 'appointment_to', 'negotation_status')
        ->findOrFail($id);
        return response()->json([
            'Item' => $Item
        ]);
    }
 
    public function edit($id)
    {
        $Item = Model::findOrFail($id);
        return response()->json([
            'Item' => $Item
        ]);
    }
 
    public function update(modelRequest $request, $id)
    {
        if($request->user_type == "employee"){   
            $validator = Validator::make($request->all(), [
                'negotation_status' => 'required|boolean',
            ]); 
            if ($validator->fails()) { // if Validate Make Error Return Message Error
                return response()->json([
                    'errors' => $validator->errors(),
                ],400);
            }   
        }
        $Item = Model::findOrFail($id);
        $Item->update($this->gteInput($request,$Item));
        return response()->json([
            'success' => 'You update data success'
        ]);
    }
 
    public function destroy($id)
    {
        $Item = Model::where('id',$id)->firstOrFail();
        $Item->delete();
        return response()->json([
            'success' => 'You delete data success'
        ]);
    }

    public function multi_delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'items'   => 'required|array',
            'items.*' => 'required|exists:users,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
        Model::whereIn('id', $request->items)->forceDelete();
        return response()->json(['success' => 'You delete data success']);
    }


    private function gteInput($request,$modelClass) {
        $input = $request->only([
            'name', 'email', 'mobile', 'mobile_code', 'user_type',
            'salary', 'appointment_from', 'appointment_to',
            "holiday", "negotation_status",
        ]);

        if(isset($modelClass) ) {

             if($request->password == null) {
                $input['password'] =  $modelClass->password;
             } else {
                $input['password'] =  bcrypt($request->password);
             }

        } else {
             $input['password'] =  bcrypt($request->password);
        }

        return  $input;
    } 
}
