<?php

use App\Models\Orders;
use App\Models\Squares;
use Illuminate\Support\Facades\Route;


Route::get('add-squares',function() {

    for($i=1001;$i<=10000;$i++) {
        Squares::create([
            'title' => ' مربع ' .$i,
            'image' => rand(1,15).'.png',
            'order' => $i
        ]);
    }

    return 'ok';

});


Route::get('add-indexes',function() {


    /*

        $json = [1,2,26,27];

        Orders::create([
            'user_id' => 1,
            'title' => 'مربع 1',
            'url' => 'https://www.youtube.com/',
            'image' => '',
            'index' => 1,
            'squares' => json_encode($json),
        ]);

        $row = Orders::first();
        $arr = json_decode($row->squares);

        $json = [3,28];

        Orders::create([
            'user_id' => 1,
            'title' => 'مربع 3',
            'url' => 'https://www.youtube.com/',
            'image' => '',
            'index' => 3,
            'squares' => json_encode($json),
        ]);

        $json = [13,14,15,16,38,39,40,41,63,64,65,66,88,89,90,91];

        Orders::create([
            'user_id' => 1,
            'title' => 'مربع 13',
            'url' => 'https://www.youtube.com/',
            'image' => '',
            'index' => 13,
            'squares' => json_encode($json),
        ]);

    */

    $json = [51,52,53];

    Orders::create([
        'user_id' => 1,
        'title' => 'مربع 51',
        'url' => 'https://www.youtube.com/',
        'image' => '',
        'index' => 51,
        'row' => 1,
        'col' => 3,
        'squares' => json_encode($json),
    ]);



    return 'ok';

});



Route::get('home-v2', function () {
    return view('user.layouts.home-v2');
});

Route::get('home-v3', function () {
    return view('user.layouts.home-v3');
});

Route::get('about-us', function () {
    return view('user.pages.about-us');
});


Route::get('contact-us', function () {
    return view('user.pages.contact-us');
});


Route::get('offers', function () {
    return view('user.pages.offers');
});


Route::get('my-blocks', function () {
    return view('user.profile.my-blocks');
});


Route::get('forgot-password', function () {
    return view('user.auth.forgot-password');
});


/*
Route::get('change-password', function () {
    return view('user.profile.change-password');
});
*/
