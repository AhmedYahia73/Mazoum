<?php

namespace App\Http\Controllers\Parking;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Intervention\Image\ImageManagerStatic as Image;

class SettingController extends Controller
{
    public function setting()
    {
        $Setting = Setting::first();
        return view('parking.setting.setting', compact('Setting'));
    }


    public function update_setting(Request $request)
    {
        $request->validate([
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

        return redirect()->back()->with('info', trans('home.update_msg'));
    }
}
