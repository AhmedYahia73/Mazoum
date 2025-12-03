<?php

use App\Models\Messages;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

$check_lang = request()->segment(1);
$lang = ($check_lang == 'en') ? 'en' : 'ar';
app()->setlocale($lang);
$prefix = Get_Main_Prefix($lang);



Route::group(['namespace' => 'User'], function () use ($prefix) {


    /** Home Page */
    Route::get('/', 'PagesController@home')->name('ar_home');

    Route::get('من-نحن', 'PagesController@about')->name('ar-about-us');

    Route::get('أتصل-بنا', 'PagesController@view_contact_us')->name('ar_view_contact_us');
    Route::post('أتصل-بنا', 'PagesController@save_contact_us')->name('ar_save_contact_us');


    Route::get('التصاميم', 'PagesController@portfolio')->name('ar_portfolio');
    Route::get('المميزات', 'PagesController@services')->name('ar_advantages');
    Route::get('خطط-الأسعار', 'PagesController@pricing')->name('ar_pricing');



    // terms-condition
    Route::get('الشروط-و-الأحكام', 'PagesController@terms_condition')->name('ar-terms-condition');

    // privacy-policy
    Route::get('سياسة-الخصوصية', 'PagesController@privacy_policy')->name('ar-privacy-policy');


    Route::post('contact-us', function (Request $request) {

        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'mobile' => 'required|numeric',
            'email' => 'required|email',
            'message' => 'required',
            //'g-recaptcha-response' => 'required'
        ]);


        $message = Messages::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'message' => $request->message
        ]);


        return redirect()->back()->with('success', 'message sent successfully');

    })->name('ar-save-contact-us');



    Route::get('ar-subscribe', function () { return redirect('/');  });
    Route::post('ar-subscribe', 'PagesController@save_subscribe')->name('ar-save-subscribe');

});




