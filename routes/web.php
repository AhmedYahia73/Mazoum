<?php

use App\Models\Orders;
use App\Models\Squares;
use App\Models\Admin;
use  App\Models\Setting;
use App\Models\Qr_Code;
use App\Models\EventUsers;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Intervention\Image\ImageManagerStatic as Image;



Route::get('event-login/{id}', 'EventUserActionsController@event_login');

Route::post('new-save-event-user-action', 'EventUserActionsController@new_save_event_action');




require_once __DIR__ . '/login.php';



require_once __DIR__ . '/chat.php';



require_once __DIR__ . '/guest.php';





//////////// Admin
Route::group(['middleware' => ['AuthAdmin:admin', 'Admin_Language', 'auth:sanctum', 'api_in_web'], 'namespace' => 'Admin', 'prefix' => 'admin'], function () {
    require_once __DIR__ . '/admin.php';
});



//////////// Member
Route::group(['middleware' => ['AuthMember:member'], 'namespace' => 'Member', 'prefix' => 'member_panel'], function () {
    require_once __DIR__ . '/member.php';
});



//////////// Parking
Route::group(['middleware' => ['AuthParking:parking', 'Admin_Language'], 'namespace' => 'Parking', 'prefix' => 'parking_panel'], function () {
    require_once __DIR__ . '/parking.php';
});




//////////// Admin
Route::group(['middleware' => ['AuthAssistant:assistant'], 'namespace' => 'Assistant', 'prefix' => 'assistant_panel'], function () {
    require_once __DIR__ . '/assistant.php';
});



Route::get('scan-car-qr/{uu_id}', function ($uu_id) {
    return view('scan-car-qr', compact('uu_id'));
});

Route::get('post-scan-car-qr', function () {
    return redirect('/');
});

Route::post('post-scan-car-qr', 'Parking\ParkingController@scan_car_qr');



////////////////////////////////////////////////////////////////////////////////////////////////

Route::get('test-send-message-v1', function () {


    $token = 'EABIy7zT1dfYBO304MlaYIQZBalGto0d1oPSCKHXEosSCsaLIdxE6QgftNNSLuhG37zirzBTMpK8HprkTRtlLyQZB1evrzBItZBW8y8LgZAYQ1pd6y64GtnMmKUZCjlY0QAZBhvu0VErD7fPzO8iz0cg0OrZBC8ovZA1F5ZCLzWa85nwaL1jWP8WYaa8yI1Ffkmvsy0QHjRrU5bSMJLS8b9bt7ZA2c0Ys8WYvlTMufprZCQ5ZCiAGTqGfzO9LcVY8S9CdpuY1PZBD1phEneQZDZD';

    $sender_id = '746157308570599';

    $phone = '201008478014';

    $template_name4 = 'wedding_data_v4_ar';

    $url4 = 'https://api.karzoun.app/CloudApi.php?token='.$token.'&sender_id='.$sender_id.'&phone='.$phone.'&template='.$template_name4;

    $response4 = SendNewTemplateCodeV1($url4);

    dd($response4);

    if ($response4 && $response4->getStatusCode() == 200) { // 200 OK

        // $response_data2 = $response2->getBody()->getContents();

        // info($response_data2);

        //dd($response_data,json_decode($response_data,true));
    }



    // $curl = curl_init();

    // $token = 'EAAMsejfnW3YBO8Dyx2A33sCNgtAcJ4TNlOBrQ3LWP32viFq2ZB2UCR7w1NF5iCOxCsn5DNZAoK9qwCISeDDA7C91bjdfVzLau1pze5huMjn0d9FFW8ri1k33o8kQelh32ayQZBvDwJtcJNJiebk7BAGT34kUUe3E0YutgnHPbafP3IZCx14OQqNp4TAfCR6o';
    // $sender_id = '344115548775193';
    // $phone = '201008478014';
    // $template_name = 'wedding_data_v1_ar';
    // $param_1 = 'ندعوك لحضور حفل زفاف السيد والاء';
    // $param_2 = 'ي سيد';
    // $url_image = 'https://6gphones.ae/mazoom/public/logo/mazoom.png?2106217949';

    // curl_setopt_array($curl, array(
    //    CURLOPT_URL => 'https://api.karzoun.app/CloudApi.php?token='.$token.'&sender_id='.$sender_id.'&phone='.$phone.'&template='.$template_name.'&param_1='.$param_1.'&param_2='.$param_2.'&image='.$url_image,
    //    CURLOPT_RETURNTRANSFER => true,
    //    CURLOPT_ENCODING => '',
    //    CURLOPT_MAXREDIRS => 10,
    //    CURLOPT_TIMEOUT => 0,
    //    CURLOPT_FOLLOWLOCATION => true,
    //    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //    CURLOPT_CUSTOMREQUEST => 'POST',
    //    CURLOPT_HTTPHEADER => array(
    //       'User-Agent: Apidog/1.0.0 (https://apidog.com)'
    //    ),
    // ));

    // $response = curl_exec($curl);

    // curl_close($curl);

    // dd('test',$response);

});


// accept-event
Route::get('accept-event', function (Request $request) {

    info('sayed v2');
    info($request->all());

});

// accept-event
Route::post('accept-event', function (Request $request) {

    info('sayed v3');
    info($request->all());

});




////////////////////////////////////////////////////////////////////////////////////////////////

Route::get('test-send-message-v1', function () {


    $token = 'EAAMsejfnW3YBO8Dyx2A33sCNgtAcJ4TNlOBrQ3LWP32viFq2ZB2UCR7w1NF5iCOxCsn5DNZAoK9qwCISeDDA7C91bjdfVzLau1pze5huMjn0d9FFW8ri1k33o8kQelh32ayQZBvDwJtcJNJiebk7BAGT34kUUe3E0YutgnHPbafP3IZCx14OQqNp4TAfCR6o';
    $sender_id = '344115548775193';
    $phone = '201008478014';
    $template_name = 'wedding_data_v1_ar';
    $param_1 = 'ندعوك لحضور حفل زفاف السيد والاء';
    $param_2 = 'ي سيد';
    $url_image = 'https://6gphones.ae/mazoom/public/logo/mazoom.png?2106217949';

    $url = 'https://api.karzoun.app/CloudApi.php?token='.$token.'&sender_id='.$sender_id.'&phone='.$phone.'&template='.$template_name.'&param_1='.$param_1.'&param_2='.$param_2.'&image='.$url_image;

    $response = SendNewTemplateCodeV1($url);

    if ($response && $response->getStatusCode() == 200) { // 200 OK

        $response_data = $response->getBody()->getContents();

        dd($response_data,json_decode($response_data,true));
    }

});







require_once __DIR__ . '/lang.php';
