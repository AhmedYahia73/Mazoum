<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests\MobileCodes as modelRequest;
use App\Http\Controllers\Controller;
use App\Models\MobileCodes as Model;

class MobileCodesController extends Controller
{
    private $view = 'admin.mobile_codes.';
    private $redirect = 'admin/mobile_codes';


    public function get_lang()
    {
        $lang = session()->get('admin_lang');

        if($lang == 'en' && $lang != null) {
            return $lang;
        } else {
            return 'ar';
        }
    }

    public function index()
    {
        $lang = $this->get_lang();

        if($lang == null) {
            $lang = 'ar';
            app()->setLocale('ar');
            session()->put('admin_lang', 'ar');
        }

        $Item = Model::get();       
         return response()->json([
            'Item' =>  $Item, 
        ]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view($this->view . 'create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(modelRequest $request)
    {

        $Item = Model::create($this->gteInput($request, null));
        return response()->json([
            'success' =>  'تم تخزين البيانات بنجاح', 
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
            'Item' =>  $Item, 
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
            'Item' =>  $Item, 
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
        $Item->update($this->gteInput($request, $Item));

        return response()->json([
            'success' =>  'تم تحديث البيانات بنجاح', 
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
        $Item = Model::findOrFail($id);
        $Item->delete();
        return response()->json([
            'success' =>  'تم حذف البيانات بنجاح', 
        ]);
    }


    private function gteInput($request, $modelClass)
    {

        $input = $request->only([
            'en_country_name','ar_country_name','code','country_code'
        ]);
 
        return  $input;
    }


}
