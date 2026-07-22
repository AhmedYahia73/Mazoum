<?php

use App\Http\Controllers\Admin\EventUersController;
use App\Http\Controllers\Api\CustomEvent\PackageController;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;
use Login\LoginController;
 
 
// webhook
Route::get('webhook', 'HomeController@webhook_v1');
Route::post('webhook', 'HomeController@new_webhook_post');

/*
Route::get('webhook', 'WhatsAppWebhookController@handle');
Route::post('webhook', 'WhatsAppWebhookController@verify');
*/

// routes/api.php
//Route::post('webhook/whatsapp', [WhatsAppWebhookController::class, 'handle']);
//Route::get('webhook/whatsapp', [WhatsAppWebhookController::class, 'verify']);
//////////// User
Route::group(['middleware' => ['IsUser'], 'prefix' => 'user'], function () {
        
    Route::controller('WattsChatController')
    ->prefix("watts_chat")->group(function () {
        Route::get('/webhook', 'verifyWebhook')->withoutMiddleware(['auth', 'throttle', 'checkPassword','CheckLang', "AuthUser", "CheckUserToken", "IsUser"]);
        Route::post('/webhook', 'receiveMessage')->withoutMiddleware(['auth', 'throttle', 'checkPassword','CheckLang', "AuthUser", "CheckUserToken", "IsUser"]);
        // backward compat
        Route::get('/verifyWebhook', 'verifyWebhook')->withoutMiddleware(['auth', 'throttle', 'checkPassword','CheckLang', "AuthUser", "CheckUserToken", "IsUser"]);
        Route::post('/receiveMessage', 'receiveMessage')->withoutMiddleware(['auth', 'throttle', 'checkPassword','CheckLang', "AuthUser", "CheckUserToken", "IsUser"]);
    });
    Route::controller('Api\CustomEvent\ChatController')
    ->prefix("chat")->group(function () {
        Route::get('/custom_users/{id}', 'custom_users');
        Route::get('/custom_msgs/{id}', 'custom_msgs')->withoutMiddleware(['auth', 'throttle', 'checkPassword','CheckLang', "AuthUser", "CheckUserToken", "IsUser"]);
        Route::get('/custom_msg_read/{id}', 'custom_msg_read');
        Route::get('/custom_msg_vistor_read/{id}', 'custom_msg_vistor_read')->withoutMiddleware(['auth', 'throttle', 'checkPassword','CheckLang', "AuthUser", "CheckUserToken", "IsUser"]);
        Route::post('/user_send_custom_msg', 'user_send_custom_msg');
        Route::post('/event_user_send_custom_msg', 'event_user_send_custom_msg')->withoutMiddleware(['auth', 'throttle', 'checkPassword','CheckLang', "AuthUser", "CheckUserToken", "IsUser"]);
        
        Route::get('/event_users/{id}', 'event_users');
        Route::get('/event_msgs/{id}', 'event_msgs')->withoutMiddleware(['auth', 'throttle', 'checkPassword','CheckLang', "AuthUser", "CheckUserToken", "IsUser"]);
        Route::get('/event_msg_read/{id}', 'event_msg_read');
        Route::get('/event_msg_vistor_read/{id}', 'event_msg_vistor_read')->withoutMiddleware(['auth', 'throttle', 'checkPassword','CheckLang', "AuthUser", "CheckUserToken", "IsUser"]);
        Route::post('/user_send_event_msg', 'user_send_event_msg');
        Route::post('/event_user_send_event_msg', 'event_user_send_event_msg')->withoutMiddleware(['auth', 'throttle', 'checkPassword','CheckLang', "AuthUser", "CheckUserToken", "IsUser"]);
    });
    
    Route::controller('Api\CustomEvent\MemoryController')
    ->prefix("memories")->group(function () {
        Route::get('/custom_memories/{id}', 'custom_memories')->withoutMiddleware(['auth', 'throttle', 'checkPassword','CheckLang', "AuthUser", "CheckUserToken", "IsUser"]);
        Route::get('/memories/{id}', 'memories')->withoutMiddleware(['auth', 'throttle', 'checkPassword','CheckLang', "AuthUser", "CheckUserToken", "IsUser"]);
        Route::post('/send_custom_memories', 'send_custom_memories')->withoutMiddleware(['auth', 'throttle', 'checkPassword','CheckLang', "AuthUser", "CheckUserToken", "IsUser"]);
        Route::post('/send_memories', 'send_memories')->withoutMiddleware(['auth', 'throttle', 'checkPassword','CheckLang', "AuthUser", "CheckUserToken", "IsUser"]);
    });
 
    Route::get('/custom_event_report/{id}', 'Api\CustomEvent\CustomEventController@custom_event_report');
    Route::get('/all_event_users/{id}', 'Api\CustomEvent\CustomEventController@all_event_users');
    Route::get('/scan_users/{id}', 'Api\CustomEvent\CustomEventController@scan_users');
    Route::get('/congratulation_msg/{id}', 'Api\CustomEvent\CustomEventController@congratulation_msg');
    Route::get('/apologize_msg/{id}', 'Api\CustomEvent\CustomEventController@apologize_msg');

    Route::post('/custom_event/{id}', 'Api\CustomEvent\CustomEventController@update');
    Route::resource('custom_event','Api\CustomEvent\CustomEventController',['names' => 'user.custom_event']);

    Route::controller('Api\CustomEvent\PackageController')
    ->prefix("package")->group(function () {
        Route::get('/', 'view');
        Route::get('/my_package', 'my_package');
        Route::post('/negotaition', 'negotaition');
        Route::get('/custom_template/{id}', 'custom_template');
        Route::get('/event_template/{id}', 'event_template');
        Route::get('/custom_details/{id}', 'custom_details');
        Route::get('/event_details/{id}', 'event_details');
        Route::post('/create_qr', 'create_qr');
        Route::get('/negotations_history', 'negotations_history');
        Route::get('/orders_list', 'orders_list');
        Route::get('/orders_history', 'orders_history');
        Route::post('/payment', 'payment');
        // ____________________________________
        Route::get('/my_custom_events', 'my_custom_events');
        // new
        Route::put('/attend_event/{id}', 'attend_event');
        Route::get('/event_open_users/{id}', 'event_open_users');
        Route::get('/custom_open_users/{id}', 'custom_open_users');

        Route::put('/attend_custom_event/{id}', 'attend_custom_event');
        Route::post('/save_event_users', 'save_event_users');
        Route::get('/event_visitors/{id}', 'event_visitors');
        // Route::get('/event_visitor_item/{id}', 'event_visitor_item');
        Route::post('/update_event_users', 'update_event_users');
        Route::delete('/destroy_user/{id}', 'destroy_user');
        //______________________________________________
        Route::get('/send_invitations/{id}', 'send_invitations');
        Route::post('/new_send_event_invitation', 'new_send_event_invitation');
        Route::post('/share_custom_invitation_watts', 'share_custom_invitation_watts');
        //______________________________________________

        Route::get('/event_users/{id}', 'event_users');    
        //______________________________________________

        Route::post('/event_family_search', 'event_family_search');
        Route::post('/save_event_family', 'save_event_family');
        Route::get('/open_event_family/{id}', 'open_event_family');
        Route::delete('/delete_event_family/{id}', 'delete_event_family');
        //______________________________________________

        Route::get('/event_report/{id}', 'event_report');    
    });

    Route::controller('Api\CustomEvent\UserController')
    ->prefix("user")->group(function () {
        Route::get('/', 'view');   
        Route::get('/custom', 'custom');   
        Route::get('/event', 'event');   
        Route::get('/lists', 'lists');    
        Route::put('/update/{id}', 'update');    
        Route::post('/add', 'create');    
    });
});

// webhook 

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
    Route::get('user/events/all_events', 'ApiEventsController@all_events');
    Route::get('user/events/apologize_msgs/{id}', 'ApiEventsController@apologize_msgs');
    Route::get('user/events/congratulation_msgs/{id}', 'ApiEventsController@congratulation_msgs');
    Route::get('user/events', 'ApiEventsController@index');
    Route::get('user/events/item/{id}', 'ApiEventsController@edit');
    Route::get('user/best_memories/{id}', 'ApiEventsController@best_memories');
    Route::get('user/best_custom_memories/{id}', 'ApiEventsController@best_custom_memories');
    Route::post('user/user_event/qr_link', 'ApiEventsController@qr_link');
    Route::post('user/events', 'ApiEventsController@store');
    Route::post('user/update-event', 'ApiEventsController@update');
    Route::get('user/event-details/{id}', 'ApiEventsController@event_details');
    Route::get('user/event-users/{id}/{type}', 'ApiEventsController@event_users_list');
    Route::get('user/event_users_count/{id}', 'ApiEventsController@event_users_count');

    // Excel Export APIs
    Route::controller('ApiExcelController')->prefix('user/excel')->group(function () {
        // Custom Event
        Route::get('/event_users',               'excel_event_users');
        Route::get('/event_family',              'excel_event_family');
        Route::get('/event_host_visitor',        'excel_event_host_visitor');
        Route::get('/event_host_qr',             'excel_event_host_qr');
        Route::get('/event_host_congrate_msg',   'excel_event_host_congrate_msg');
        Route::get('/event_host_apologize_msg',  'excel_event_host_apologize_msg');
        Route::get('/event_host_apologize',      'excel_event_host_apologize');
        Route::get('/event_host_confirm',        'excel_event_host_confirm');
        Route::get('/qr_count/{id}',             'excel_qr_count');
        Route::get('/confirm_count/{id}',        'excel_confirm_count');
        Route::get('/apologize_count/{id}',      'excel_apologize_count');
        // Regular Event
        Route::get('/all_invited_users/{id}',          'excel_all_invited_users');
        Route::get('/event_qr_details/{id}',           'excel_event_qr_details');
        Route::get('/confirmed_event_details/{id}',    'excel_confirmed_event_details');
        Route::get('/confirmed_users_web_chat/{id}',   'excel_confirmed_users_web_chat');
        Route::get('/not_attend_event_details/{id}',   'excel_not_attend_event_details');
        Route::get('/hold_event_details/{id}',         'excel_hold_event_details');
        Route::get('/failed_event_details/{id}',       'excel_failed_event_details');
        Route::get('/non_attendance_event_details/{id}','excel_non_attendance_event_details');
        Route::get('/qr_sent_event_details/{id}',      'excel_qr_sent_event_details');
    });
    Route::get('user/delete-event/{id}', 'ApiEventsController@destroy');
    Route::get('login-user/{id}', [EventUersController::class, 'login_user']);

    // User Events 
    Route::put('user/attend_event/{id}', 'ApiEventUersController@attend_event');
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