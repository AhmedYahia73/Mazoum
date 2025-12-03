<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManagerStatic as Image;

class SettingController extends Controller
{
    public function setting()
    {
        $Setting = Setting::first();

        return response()->json([
            'Setting' =>  $Setting, 
        ]);   
    }


    public function update_setting(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'email' => 'required|email',
            'en_website_name' => 'required',
            'ar_website_name' => 'required',
            'en_description' => 'required',
            'ar_description' => 'required',
            'en_key_words' => 'required',
            'ar_key_words' => 'required',
            'mobile' => 'required',
            'logo' => 'nullable|image',
        ]);
        if ($validation->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validation->errors(),
            ],400);
        } 

        $Setting = Setting::first();

        $data = $request->except(['_token', 'logo']);

        $Setting->update($data);

        if ($request->file('logo') != null) {
            $path = Upload_Path();

            $logo = 'logo' . '.' . $request->file('logo')->extension();

            $logo_path = $path . '/' . $logo;
            $logo_img = $request->file('logo')->getRealPath();

            Image::make($logo_img)->save($logo_path, 100);

            $Setting->update(['logo' => $logo]);
        }

        return response()->json([
            'success' =>  'تم تحديث البيانات بنجاح', 
        ]);  
    }
}
