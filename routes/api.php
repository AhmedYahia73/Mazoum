<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Admin;

use Login\LoginController;

// webhook
Route::get('webhook', 'HomeController@webhook_v1');
Route::post('webhook', 'HomeController@webhook_post');

Route::controller(LoginController::class)->group(function () {
    Route::post('login_admin', 'login_admin');
});



Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


////////////////////// Gest User
Route::group(['middleware' => ['checkPassword','CheckLang'],'namespace' => 'Api'], function () {

    // register
    Route::get('register', function () {
        return response()->json([ 'status' => false, 'errNum' => '404', 'msg' => 'bad request' ]);
    });

    Route::post('register', 'AuthController@register');


    // login
    Route::get('login', function () {
        return response()->json([ 'status' => false, 'errNum' => '404', 'msg' => 'bad request' ]);
    });

    Route::post('login', 'AuthController@login');



    // update-profile
    Route::get('update-profile', function () {
        return response()->json([ 'status' => false, 'errNum' => '404', 'msg' => 'bad request' ]);
    });

    Route::post('update-profile', 'AuthController@update_profile');



    // remove-user
    Route::get('remove-user', function () {
        return response()->json([ 'status' => false, 'errNum' => '404', 'msg' => 'bad request' ]);
    });

    Route::post('remove-user', 'AuthController@remove_user');


    Route::get('profile', 'AuthController@profile');



  	Route::get('mobile-scan-qr/{uu_id}', 'AuthController@mobile_scan_qr');

  	Route::get('mobile-custom-scan-qr/{uu_id}', 'AuthController@mobile_custom_scan_qr');





    // home
    Route::get('home', 'HomeController@home');
    Route::get('how_to_use', 'HomeController@how_to_use');


    // notifications
    Route::get('notifications/{id?}', 'HomeController@notifications');



    //////////////////////////////////////////////////////////////// user

    // Events
    Route::get('user/events', 'ApiEventsController@index');
    Route::post('user/events', 'ApiEventsController@store');
    Route::post('user/update-event', 'ApiEventsController@update');
    Route::get('user/event-details/{id}', 'ApiEventsController@event_details');
    Route::get('user/delete-event/{id}', 'ApiEventsController@destroy');

    // User Events
    Route::post('user/save-user-event', 'ApiEventUersController@save_event_users');
    Route::post('user/edit-user-event/{event_user_id}', 'ApiEventUersController@edit_event_user');

    Route::post('user/send-user-event-invitations', 'ApiEventUersController@send_event_users');
    Route::post('user/send-user-reminder-invitations', 'ApiEventUersController@send_reminder_invitations');
    Route::get('user/delete-user-event/{id}', 'ApiEventUersController@delete_user_event');


    Route::post('user/replay-event-message', 'ApiEventUersController@send_custom_message');



    Route::get('user/login-user-using-qr/{id}', 'ApiEventUersController@login_user_using_qr');
    Route::get('user/send-qr-again/{id}', 'ApiEventUersController@send_qr');
    Route::get('user/delete-event-messages/{id}/{type}', 'ApiEventUersController@delete_event_messages');




    // save_event_family
    Route::post('save_event_family', 'ApiEventFamilyController@save_event_family');

    // update_event_family
    Route::post('update_event_family', 'ApiEventFamilyController@update_event_family');

    // destroy
    Route::get('event_family/destroy/{id}', 'ApiEventFamilyController@delete_event_family');

    // open_event_family
    Route::get('open_event_family/{id}', 'ApiEventFamilyController@open_event_family');


    /////////////////////////////////////////////////////////////////
});





// accept-event
Route::post('accept-event', function (Request $request) {

    info('sayed');
    info($request->all());

});



Route::post('accept-event', 'EventsApiController@accept_event');

Route::post('refuse-event', 'EventsApiController@refuse_event');

// Route::post('confirm-send-congratulations', 'EventsApiController@confirm_send_congratulations');

// Route::post('confirm-send-apology', 'EventsApiController@confirm_send_apology');

Route::post('save-congratulation-msg', 'EventsApiController@save_congratulation_msg');

Route::post('save-apology-msg', 'EventsApiController@save_apology_msg');


Route::post('location-event', 'EventsApiController@location_event');

Route::post('event-date', 'EventsApiController@event_date');

Route::post('resend-qr-code', 'EventsApiController@resend_qr_code');




Route::post('confirm-reservation', 'ReservationApiController@confirm_reservation');

Route::post('cancel-reservation', 'ReservationApiController@cancel_reservation');






