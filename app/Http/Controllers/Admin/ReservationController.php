<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests\Reservation as modelRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\Reservation as Model;
use App\Models\Setting;


class ReservationController extends Controller
{

    private $view = 'admin.reservation.';
    private $redirect = 'admin/reservation';


    public function get_lang()
    {
        $lang = session()->get('admin_lang');

        if($lang == 'en' && $lang != null) {
            return $lang;
        } else {
            return 'ar';
        }
    }




  	// send_reservation_to_paid
    public function send_reservation_to_paid(Request $request)
    { 
        $validator = Validator::make($request->all(), [
          	'mobile'  => 'required|numeric',
            'message' => 'required',
            'reservation_id' => 'required|exists:reservation,id',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }  
        $setting = Setting::first(); 

        $reservation_id = $request->reservation_id;

        $item = Model::where('id', $reservation_id)->firstOrFail();

        try {

          $mobile = $request->mobile;

          //$to = $code.$mobile;
          $to = $mobile;
          $to = str_replace("+","",$to);

          $template_name = 'car_msg6';
          $language = 'ar';

          $message = $request->message;


          $phone_numer_id = $setting->phone_numer_id;
          $token = $setting->access_token;

          $sender_id = $setting->sender_id;

          // $response = SendTemplateV10($to,$template_name,$language,$message,$phone_numer_id,$token);

          $url = 'https://api.karzoun.app/CloudApi.php?token='.$token.'&sender_id='.$sender_id.'&phone='.$to.'&template='.$template_name.'&param_1='.$message;

          $response = SendNewTemplateCodeV1($url);

          //$response = SendTemplateV10($to,$template_name,$language,$message,$phone_numer_id,$token);

          if ($response != null && $response->getStatusCode() == 200) {

            $body = $response->getBody();
            $data = json_decode($body, true);

            return response()->json([
                'success' =>  'تم الأرسال بنجاح', 
            ]); 

          } else {

            return response()->json([
                'errors' =>  'فشل الارسال', 
            ]);
          }

        } catch(\Exception $e) {
            dd($e->getMessage(), $e->getLine());
        }

    }



  	// send_reservation_info_to_user
    public function send_reservation_info_to_user(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reservation_id' => 'required|exists:reservation,id',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }  
        $setting = Setting::first();
  
        $reservation_id = $request->reservation_id;

        $item = Model::where('id', $reservation_id)->firstOrFail();

        try {

          $mobile = $item->mobile;

          //$to = $code.$mobile;
          $to = $mobile;
          $to = str_replace("+","",$to);

          $template_name = 'reservation';
          $language = 'ar';

          $phone_numer_id = $setting->phone_numer_id;
          $token = $setting->access_token;

          $sender_id = $setting->sender_id;

          // $response = SendTemplateV10($to,$template_name,$language,$message,$phone_numer_id,$token);

          $str = '&param_1='.$item->event_name.'&param_2='.$item->event_date.'&param_3='.$item->event_address.'&param_4='.$item->package_price.'&param_5='.$item->events_count.'&param_6='.$item->employees_count.'&image='.$item->image;

          //dd($str);

          $url = 'https://api.karzoun.app/CloudApi.php?token='.$token.'&sender_id='.$sender_id.'&phone='.$to.'&template='.$template_name.$str;

          $response = SendNewTemplateCodeV1($url);

          //dd($response);
          //$response = SendTemplateV10($to,$template_name,$language,$message,$phone_numer_id,$token);
        //   dd($response->getStatusCode());

          if ($response != null && $response->getStatusCode() == 200) {

            $body = $response->getBody();
            $data = json_decode($body, true);

            // dd($data);

            return response()->json([
                'success' =>  'تم الأرسال بنجاح', 
            ]);  

          } else {

            return response()->json([
                'errors' =>  'فشل الارسال', 
            ]); 

          }

        } catch(\Exception $e) {
            dd($e->getMessage(), $e->getLine());
        }

        dd('error-v2');

    }



    public function index(Request $request)
    {
        $lang = $this->get_lang();

        if($lang == null) {
            $lang = 'ar';app()->setLocale('ar');session()->put('admin_lang','ar');
        }
        $query = Model::query();

        // Search
        if ($request->search) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('employee_name', 'like', "%$search%")
                ->orWhere('office_name', 'like', "%$search%")
                ->orWhere('event_name', 'like', "%$search%")
                ->orWhere('event_address', 'like', "%$search%")
                ->orWhere('mobile', 'like', "%$search%");
            });
        }

        // Pagination
        $Item = $query->paginate(15); // عدد العناصر فى الصفحة

        return response()->json([
            'Item' => $Item,
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
        $Item->update($this->gteInput($request,$Item));

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


    private function gteInput($request,$modelClass) {

        $input = $request->only([
            'event_name', 'event_date', 'event_address', 'events_count',
            'package_price', 'mobile', 'gender', 'employees_count',
            'employee_name','office_name'
        ]);

      	$path = 'images';

        if($request->file('image') != null) {

            $extension = $request->file('image')->extension();
            $filename = uniqid() . '.' . $extension;
            $request->file('image')->move($path, $filename);

            $input['image'] = $filename;
        }

        return  $input;
    }


}
