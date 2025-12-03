<?php

namespace App\Http\Controllers\Parking;

use Illuminate\Http\Request;
use App\Http\Requests\Admin as modelRequest;
use App\Http\Controllers\Controller;
use App\Models\Admin as Model;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function change_lang($lang)
    {


        if($lang == 'en' || $lang == 'ar') {
            session()->put('parking_lang', $lang);
            app()->setLocale($lang);
            return redirect()->back();
        } else {
            session()->put('parking_lang', 'ar');
            app()->setLocale('ar');
            return redirect('/parking_panel');
        }
    }

    public function home()
    {
        $msg = trans('home.welcome_msg') . Auth::guard('parking')->user()->name;

        return view('parking.layouts.home', compact('msg'));
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $Item = Model::all();
        return view('parking.manager.index', compact('Item'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('parking.manager.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(modelRequest $request)
    {
        Model::create($this->gteInput($request, null));
        return redirect('parking_panel/manager')->with('success', trans('home.save_msg'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        return view('parking.manager.edit', [ 'Item' => $Item ]);
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
        $Item->update($this->gteInput($request, $Item));
        return redirect()->back()->with('info', trans('home.update_msg'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $Item = Model::where('id', $id)->where('status', 0)->firstOrFail();
        $Item->delete();
        return redirect('parking_panel/manager')->with('error', trans('home.delete_msg'));
    }


    private function gteInput($request, $modelClass)
    {

        $input = $request->only(['name','email' ,'mobile']);

        if(isset($modelClass)) {

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
