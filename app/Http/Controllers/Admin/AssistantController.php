<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests\Assistant as modelRequest;
use App\Http\Controllers\Controller;
use App\Models\User as Model;
use App\Models\Assistant;
use Illuminate\Support\Facades\Auth;

class AssistantController extends Controller
{


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $Item = Model::where('user_type','employee')->get([
            'id', 'name', 'mobile', 'email', 'status',
            'employee_gender', 'mobile_code'
        ]);
        return response()->json([
            'assistants' => $Item
        ]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.assistant.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(modelRequest $request)
    {
        Model::create($this->gteInput($request,null));
        
        return response()->json([
            'success' => 'You add data success'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $Item = Model::
        select('id', 'name', 'mobile', 'email', 'status',
        'employee_gender', 'mobile_code')
        ->findOrFail($id);
        return response()->json([
            'Item' => $Item
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $Item = Model::findOrFail($id);
        return response()->json([
            'Item' => $Item
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(modelRequest $request, $id)
    {
        $Item = Model::findOrFail($id);
        $Item->update($this->gteInput($request,$Item));
        return response()->json([
            'success' => 'You update data success'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $Item = Model::where('id',$id)->firstOrFail();
        $Item->delete();
        return response()->json([
            'success' => 'You delete data success'
        ]);
    }


    private function gteInput($request,$modelClass) {

        $input = $request->only(['name','email' ,'mobile','email','mobile_code']);
      
        $input['user_type'] =  'employee';

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
