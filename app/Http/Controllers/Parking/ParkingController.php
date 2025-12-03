<?php

namespace App\Http\Controllers\Parking;

use Illuminate\Http\Request;
use App\Http\Requests\Parking as modelRequest;
use App\Http\Controllers\Controller;
use App\Models\Parking as Model;
use App\Models\Qr_CodeParking;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Intervention\Image\ImageManagerStatic as Image;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ParkingController extends Controller
{
    private $view = 'parking.parking.';
    private $redirect = 'parking_panel/parking';


    public function get_lang()
    {
        $lang = session()->get('parking_lang');

        if($lang == 'en' && $lang != null) {
            return $lang;
        } else {
            return 'ar';
        }
    }
  
  	
  
  	// delete_selected_items
    public function delete_selected_items(Request $request)
    {

        if($request->items != null && ! empty($request->items)) {

            $data = Model::whereIn('id', $request->items)->get();

            foreach($data as $Item) {

              $Item->delete();

            }

            return redirect()->back()->with('info', 'تم حذف العناصر المختاره');

        } else {
            return redirect()->back()->with('error', 'من فضلك اختر عنصر واحد علي الاقل');
        }

    }




    public function scan_car_qr(Request $request)
    {

        $request->validate([
            'uu_id' => 'required',
            'serial_number' => 'required',
        ]);

        $Item = Model::where('uu_id',$request->uu_id)->where('serial_number',$request->serial_number)->first();

        if($Item != null) {

            if($Item->parking_status == 'starting') {

                $setting = Setting::first();

                $parking = $Item;
                $mobile = $parking->phone;
                $to = $mobile;

                $template_name = 'car_msg2';
                $language = 'ar';
                $phone_numer_id = $setting->phone_numer_id;
                $token = $setting->access_token;

                $response = SendCarTemplateV2($to, $template_name, $language, $phone_numer_id, $token);

                if ($response != null && $response->getStatusCode() == 200) {

                    $parking->update(['parking_status' => 'leaving']);

                    return redirect()->back()->with('success','تم طلب خروج السياره بنجاح');

                } else {
                    return redirect()->back()->with('error','عفوا لقد حدث خطأ ما برجاء المحاولة مره اخري');
                }

            } else {
                return redirect()->back()->with('error','عفوا تم طلب خروج السياره بالفعل');
            }

        } else {
            return redirect()->back()->with('error','عفوا الكود غير صحيح');
        }
    }


    public function sent_message($id)
    {

        $Item = Model::findOrFail($id);

        if($Item->phone != null) {

            $setting = Setting::first();

            $mobile = $Item->phone;
            $to = $mobile;

            $phone_numer_id = $setting->phone_numer_id;
            $token = $setting->access_token;

            $template_name = 'car_msg1';
            $language = 'ar';

            $response = SendCarTemplateV1($to, $template_name, $language, $phone_numer_id, $token);

            if ($response != null && $response->getStatusCode() == 200) {

                $body = $response->getBody();
                $data = json_decode($body, true);

                if(array_key_exists('messages', $data) && count($data['messages']) >= 0 && array_key_exists('id', $data['messages'][0])) {
                    $message_id = $data['messages'][0]['id'];
                } else {
                    $message_id = 0;
                }

                $Item->update([
                    'status' => 'sent',
                    'message_id' => $message_id
                ]);

                return redirect('parking_panel/parking/' . $Item->id . '/edit')->with('success', 'تم الارسال بنجاح');

            } else {

                $Item->update([
                    'status' => 'failed',
                ]);

                return redirect('parking_panel/parking/' . $Item->id . '/edit')->with('error', 'لقد فشلت عملية الارسال');
            }

        } else {
            return redirect('parking_panel/parking/' . $Item->id . '/edit')->with('error', 'برجاء تحديث رقم الموبيل أولا');
        }


    }


    public function confirm_leave_parking($id)
    {

        $Item = Model::findOrFail($id);

        $Item->update([
            'parking_status' => 'closed',
            'out_time' => Carbon::now()
        ]);

        return redirect('parking_panel/reports')->with('success', 'لقد تمت عملية الخروج بنجاح');
    }
  
   	public function exit_parking($id)
    {

        $Item = Model::findOrFail($id);

        $Item->update([
            'parking_status' => 'closed',
            'out_time' => Carbon::now()
        ]);

        return redirect('parking_panel/reports')->with('success', 'لقد تمت عملية الخروج بنجاح');
    }


    public function reports()
    {
        $Item = Model::where('parking_status', 'closed')->get();
        return view($this->view . 'reports', compact('Item'));
    }


    public function leave_parking()
    {
        $Item = Model::where('parking_status', 'leaving')->get();
        return view($this->view . 'leave_parking', compact('Item'));
    }

    public function index()
    {
        $Item = Model::where('parking_status', 'starting')->get();
        return view($this->view . 'index', compact('Item'));
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

        $uu_id = $Item->uu_id;
        $bg = 'mazoom.jpeg';

        $image_name = $uu_id . '-car-qr.png';

        Qr_CodeParking::create([
            'parking_id' => $Item->id,
            'uu_id' => $uu_id,
            'qr' => $image_name,
            'counter' => 0
        ]);

        $link = asset('scan-car-qr/' . $uu_id);
        $qr_code_path = 'qr_code/' . $image_name;
        QrCode::size(400)->format('png')->generate($link, $qr_code_path);

        Image::make($bg)->insert($qr_code_path, 'center', 0, 400)->save($qr_code_path, 100);

        $destination = public_path($qr_code_path);

        $new_img = Image::make($destination);

        $new_img->text($Item->serial_number, 420, 670, function ($font) {
            $font->file(public_path('font/OpenSans-Italic.ttf'));
            $font->size(80);
            $font->color('#000');
        });

        $now = Carbon::now()->format('Y-m-d h:i A');

        $new_img->text($now, 350, 1570, function ($font) {
            $font->file(public_path('font/OpenSans-Italic.ttf'));
            $font->size(50);
            $font->color('#000');
        });

        $new_img->save($destination);

        return redirect('parking_panel/parking/' . $Item->id . '/edit')->with('success', trans('home.save_msg'));
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
        return view($this->view . 'edit', [ 'Item' => $Item ]);
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
        $Item = Model::findOrFail($id);
        $Item->delete();
        return redirect()->back()->with('error', trans('home.delete_msg'));
    }


    private function gteInput($request, $modelClass)
    {

        $input = $request->only([
            'user_name','mobile','mobile_code', 'car_type', 'car_number', 'location',
        ]);

      	$input['phone'] = $request->mobile_code.$request->mobile;
      
        if(! isset($modelClass)) {
            $input['car_code'] = $request->car_code;
            $input['serial_number'] = $this->get_serial_number();
            $input['uu_id'] = (string) Str::uuid();
            $input['entry_time'] = Carbon::now();
            $input['parking_status'] = 'starting';
        } else {
            $input['car_code'] = $modelClass->car_code;
            $input['serial_number'] = $modelClass->serial_number;
            $input['uu_id'] = $modelClass->uu_id;
            $input['entry_time'] = $modelClass->entry_time;
            $input['parking_status'] = $modelClass->parking_status;
        }

        return  $input;
    }


    private function get_serial_number()
    {

        $serial_number = mt_rand(10000, 9999999);

        while(Model::where('serial_number', $serial_number)->exists()) {
            $serial_number = rand(10000, 9999999);
        }

        return $serial_number;
    }
}
