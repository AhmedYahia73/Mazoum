<?php

use Illuminate\Support\Facades\Route;

// Home

Route::get('/', 'ProfileController@home');



// Setting

Route::get('profile', 'ProfileController@profile');
Route::post('profile', 'ProfileController@update_profile');

// Events
Route::resource('events', 'EventsController', ['names' => 'assistant_panel.events']);
Route::get('events/destroy/{id}', 'EventsController@destroy');
Route::get('events/show-pdf/{id}', 'EventsController@show_pdf');


Route::get('event-details/{id}', 'EventsController@edit');



// save_event_users
Route::get('save_event_users', function() {
    return redirect('assistant_panel');
});
Route::post('save_event_users', 'EventUersController@save_event_users');


// update_event_users
Route::get('update_event_users', function () {
    return redirect('assistant_panel');
});
Route::post('update_event_users', 'EventUersController@update_event_users');


// send_event_users
Route::get('send_event_users', function () {
    return redirect('assistant_panel');
});
Route::post('send_event_users', 'EventUersController@send_event_users');


// event_users_search
Route::get('event_users_search', 'EventUersController@event_users_search');


// delete event_users
Route::get('event_users/destroy/{id}', 'EventUersController@destroy');

Route::get('event-user-history/{id}', 'EventUersController@event_user_history');

Route::get('send-qr/{id}', 'EventUersController@send_qr');

Route::get('all-invited-users/{id}', 'EventUersController@all_invited_users');
Route::get('event-qr-details/{id}', 'EventUersController@event_qr_details');
Route::get('confirmed-event-details/{id}', 'EventUersController@confirmed_event_details');
Route::get('not-attend-event-details/{id}', 'EventUersController@not_attend_event_details');
Route::get('hold-event-details/{id}', 'EventUersController@hold_event_details');
Route::get('failed-event-details/{id}', 'EventUersController@failed_event_details');
Route::get('qr-sent-event-details/{id}', 'EventUersController@qr_sent_event_details');
Route::get('congratulations-event-messages-details/{id}', 'EventUersController@congratulations_event_messages_details');
Route::get('delete-event-messages/{id}/{type}', 'EventUersController@delete_event_messages');


Route::get('event-messages/{id}', 'EventUersController@event_messages');


Route::post('send-custom-message', 'EventUersController@send_custom_message');
Route::post('delete_event_users', 'EventUersController@delete_event_users');



// save_event_family
Route::get('save_event_family', function() {
    return redirect('assistant_panel');
});
Route::post('save_event_family', 'EventUersController@save_event_family');


// update_event_family
Route::get('update_event_family', function () {
    return redirect('assistant_panel');
});
Route::post('update_event_family', 'EventUersController@update_event_family');


// event_family_search
Route::get('event_family_search', 'EventUersController@event_family_search');


// destroy
Route::get('event_family/destroy/{id}', 'EventUersController@delete_event_family');


Route::get('open_event_family/{id}', 'EventUersController@open_event_family');








// login-user
Route::get('login-user/{id}', 'EventUersController@login_user');


// event-report
Route::get('event-report/{id}', 'EventUersController@event_report');


// send-congratulations
Route::get('send-congratulations/{id}', 'EventUersController@send_congratulations');





// Uses
Route::resource('custom_events', 'CustomEventController', ['names' => 'assistant_panel.custom_events']);
Route::get('custom_events/destroy/{id}', 'CustomEventController@destroy');



// custom_event_users_search
Route::get('custom_event_users_search', 'CustomEventController@event_users_search');



// save_event_users
Route::get('save_custom_event_users', function() {
    return redirect('assistant_panel');
});
Route::post('save_custom_event_users', 'CustomEventController@save_event_users');


// update_custom_event_users
Route::get('update_custom_event_users', function () {
    return redirect('assistant_panel');
});
Route::post('update_custom_event_users', 'CustomEventController@update_event_users');


// delete event_users
Route::get('custom_event_users/destroy/{id}', 'CustomEventController@delete_event_users');

// import
Route::post('custom-event-user-import', 'CustomEventController@import');



// save_event_family
Route::get('save_custom_event_family', function() {
    return redirect('assistant_panel');
});
Route::post('save_custom_event_family', 'CustomEventController@save_event_family');


// update_custom_event_family
Route::get('update_custom_event_family', function () {
    return redirect('assistant_panel');
});
Route::post('update_custom_event_family', 'CustomEventController@update_event_family');


// event_family_search
Route::get('custom_event_family_search', 'CustomEventController@event_family_search');


// destroy
Route::get('custom_event_family/destroy/{id}', 'CustomEventController@delete_event_family');


Route::get('open_custom_event_family/{id}', 'CustomEventController@open_event_family');
