<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests\Admin as modelRequest;
use App\Http\Controllers\Controller;
use App\Models\Admin as Model;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{

    public function change_lang($lang) {


        if($lang == 'en' || $lang == 'ar') {
            session()->put('admin_lang',$lang);
            app()->setLocale($lang);
            return redirect()->back();
        } else {
            session()->put('admin_lang','ar');
            app()->setLocale('ar');
            return redirect('/admin_panel');
        }
    }

     public function home()
    {
        $msg = trans('home.welcome_msg') . Auth::guard('admin')->user()->name;

        return view('admin.layouts.home',compact('msg'));
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $Item = Model::all();
        return response()->json([
            'admins' => $Item
        ]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.manager.create');
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
            "success" => "You add data success"
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
        $Item = Model::findOrFail($id);
        return response()->json([
            "Item" => $Item
        ]);;
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
            "Item" => $Item
        ]);;
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
            "success" => "You update data success"
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
        $Item = Model::where('id',$id)->where('status',0)->firstOrFail();
        $Item->delete();
        return response()->json([
            "success" => "You delete data success"
        ]);
    }


    private function gteInput($request,$modelClass) {

        $input = $request->only(['name','email' ,'mobile']);

        if(isset($modelClass) ) {

             $input['status'] =  $modelClass->status;

             if($request->password == null) {
                $input['password'] =  $modelClass->password;
             } else {
                $input['password'] =  bcrypt($request->password);
             }

        } else {
             $input['status'] =  0;
             $input['password'] =  bcrypt($request->password);
        }

        return  $input;
    }





}
