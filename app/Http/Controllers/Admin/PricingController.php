<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests\Pricing as modelRequest;
use App\Http\Controllers\Controller;
use App\Models\Pricing as Model;
use App\Models\Currency;
use Response;

class PricingController extends Controller
{

    private $view = 'admin.pricing.';
    private $redirect = 'admin/pricing';


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
            $lang = 'ar';app()->setLocale('ar');session()->put('admin_lang','ar');
        }

        $Item = Model::get(['id','en_title','ar_title','users_count','price']);
        $currencies = Currency::select("id", "en_name", "ar_name")
        ->get();
        return response()->json([
            "success" => $Item,
            "currencies" => $currencies,
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

        $Item = Model::create($this->gteInput($request,null));
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
            "Item" => $Item
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
        $Item = Model::findOrFail($id);
        $Item->delete();
        return response()->json([
            "success" => "You delete data success"
        ]);
    }


    private function gteInput($request,$modelClass) {

        $input = $request->only([
            'en_title', 'ar_title', 'users_count','price'
        ]);
      
      	if(isset($request->send_invitation) && $request->send_invitation == 'yes') {
        	$input['send_invitation'] = 'yes';
        } else {
        	$input['send_invitation'] = 'no';
        }
      
      	if(isset($request->confirm_attendance) && $request->confirm_attendance == 'yes') {
        	$input['confirm_attendance'] = 'yes';
        } else {
        	$input['confirm_attendance'] = 'no';
        }
      
      	if(isset($request->confirm_apology) && $request->confirm_apology == 'yes') {
        	$input['confirm_apology'] = 'yes';
        } else {
        	$input['confirm_apology'] = 'no';
        }
      
      	if(isset($request->reminder_before_invitation) && $request->reminder_before_invitation == 'yes') {
        	$input['reminder_before_invitation'] = 'yes';
        } else {
        	$input['reminder_before_invitation'] = 'no';
        }
      
      	if(isset($request->party_employee) && $request->party_employee == 'yes') {
        	$input['party_employee'] = 'yes';
        } else {
        	$input['party_employee'] = 'no';
        }
      
      	if(isset($request->attendance_report_after_invitation) && $request->attendance_report_after_invitation == 'yes') {
        	$input['attendance_report_after_invitation'] = 'yes';
        } else {
        	$input['attendance_report_after_invitation'] = 'no';
        }
      
      	if(isset($request->send_congratulations_after_invitation) && $request->send_congratulations_after_invitation == 'yes') {
        	$input['send_congratulations_after_invitation'] = 'yes';
        } else {
        	$input['send_congratulations_after_invitation'] = 'no';
        }
      
      
      	if(isset($request->congratulations_messages) && $request->congratulations_messages == 'yes') {
        	$input['congratulations_messages'] = 'yes';
        } else {
        	$input['congratulations_messages'] = 'no';
        }


        return  $input;
    }


}
