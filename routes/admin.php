<?php

use Illuminate\Support\Facades\Route;

// Home

Route::get('/', 'AdminController@home');


// Admins

Route::resource(
    'manager',
    'AdminController',
    ['names' => 'admin.manager']
);

// // Admin Update Password
// Route::patch('manager/update_password/{id}', 'AdminController@UpdatePass')->name('admin.manager.UpdatePass');

Route::get('manager/destroy/{id}', 'AdminController@destroy');


// Assistant

Route::resource(
    'assistant',
    'AssistantController',
    ['names' => 'admin.assistant']
);

Route::get('assistant/destroy/{id}', 'AssistantController@destroy');



// Setting
Route::get('setting', 'SettingController@setting');
Route::post('setting', 'SettingController@update_setting');


// Users
Route::resource('users','UsersController',['names' => 'admin.users']);
Route::get('users/destroy/{id}', 'UsersController@destroy');
Route::get('user-invoice/destroy/{id}', 'UsersController@delete_invoice');


// currency
Route::resource('currency','CurrencyController',['names' => 'admin.currency']);
Route::get('currency/destroy/{id}', 'CurrencyController@destroy');


// Packages
Route::resource('packages','PackagesController',['names' => 'admin.packages',
    'except' => ['update']]);
Route::get('packages/destroy/{id}', 'PackagesController@destroy');
Route::post('packages/{id}', 'PackagesController@update')->name('admin.packages.update');

// Pricing
Route::resource('pricing','PricingController',['names' => 'admin.pricing']);
Route::get('pricing/destroy/{id}', 'PricingController@destroy');


// Uses
Route::resource('uses', 'UsesController', ['names' => 'admin.uses']);
Route::get('uses/destroy/{id}', 'UsesController@destroy');


// Desgins
Route::resource('desgins', 'DesginsController', ['names' => 'admin.desgins']);
Route::get('desgins/destroy/{id}', 'DesginsController@destroy');
Route::get('desgins/show-pdf/{id}', 'DesginsController@show_pdf');


// Web Desgins
Route::resource('desgins', 'DesginsController', [
    'names' => 'admin.desgins',
    'except' => ['update'],
]);
Route::post('desgins/{id}', 'DesginsController@update');

Route::resource('web_desgins', 'WebDesginsController', [
    'names' => 'admin.web_desgins',
    'except' => ['update'],
]);
Route::post('web_desgins/{id}', 'WebDesginsController@update');
Route::get('web_desgins/destroy/{id}', 'WebDesginsController@destroy');
Route::get('web_desgins/show-pdf/{id}', 'WebDesginsController@show_pdf');


// Events
Route::post('events/update_location', 'EventsController@update_location');
Route::get('events/assistant_lists', 'EventsController@assistant_lists');
Route::get('events/users_lists', 'EventsController@users_lists');
Route::resource('events', 'EventsController', [
    'names' => 'admin.events',
    'except' => ['update'],
]);

Route::post('events/{id}', 'EventsController@update');
Route::get('sa-events', 'EventsController@sa_events');


Route::patch('events/update_event_package/{id}', 'EventsController@update_event_package')->name('admin.events.update_event_package');


Route::get('events/destroy/{id}', 'EventsController@destroy');
Route::get('events/show-pdf/{id}', 'EventsController@show_pdf');

Route::get('closed-events', 'EventsController@closed_events');
Route::get('current-events', 'EventsController@current_events');
Route::get('deleted-events', 'EventsController@deleted_events');


Route::get('sa-closed-events', 'EventsController@sa_closed_events');
Route::get('sa-current-events', 'EventsController@sa_current_events');
Route::get('sa-deleted-events', 'EventsController@sa_deleted_events');


Route::get('current-event/{id}', 'EventsController@current_event');
Route::get('close-event/{id}', 'EventsController@close_event');
Route::get('un-close-event/{id}', 'EventsController@un_close_event');


//


Route::get('events/{id}/event-visitors', 'EventsController@event_visitors');
Route::get('events/{id}/send-events', 'EventsController@send_events');
Route::get('events/{id}/event-report', 'EventsController@event_report');
Route::get('events/{id}/event-users', 'EventsController@event_users');
Route::get('events/{id}/event-location', 'EventsController@event_location');
Route::get('events/{id}/enter-event', 'EventsController@enter_event');
Route::get('events/{id}/scanner', 'EventsController@scanner');
Route::get('events/{id}/my-package', 'EventsController@my_package');
Route::get('events/{id}/chat-list', 'EventsController@chat_list');


// delete_events
Route::post('delete_events', 'EventsController@delete_events');


// Event Users

// ---------------------------------------------
// save_event_users
Route::get('save_event_users', function() {
    return redirect('admin');
});
Route::post('save_event_users', 'EventUersController@save_event_users');


// update_event_users
Route::get('update_event_users', function () {
    return redirect('admin');
});
Route::post('update_event_users', 'EventUersController@update_event_users');


// send_event_users
Route::get('send_event_users', function () {
    return redirect('admin');
});
Route::post('send_event_users', 'EventUersController@send_event_users');


// event_users_search
Route::get('event_users_search', 'EventUersController@event_users_search');


// event_messages_search
Route::get('event_messages_search', 'EventUersController@event_messages_search');



// delete event_users
Route::get('event_users/destroy/{id}', 'EventUersController@destroy');

Route::get('event-user-history/{id}', 'EventUersController@event_user_history');

Route::get('send-qr/{id}', 'EventUersController@send_qr');
Route::get('send-new-qr/{id}', 'EventUersController@send_new_qr');


Route::get('accept-user-event/{id}', 'EventUersController@accept_user_event');
Route::get('refuse-user-event/{id}', 'EventUersController@refuse_user_event');


Route::get('qr-is-send/{id}', 'EventUersController@qr_is_send');
Route::get('is-send-event/{id}', 'EventUersController@is_send_event');



Route::get('all-invited-users/{id}', 'EventUersController@all_invited_users');
Route::get('event-qr-details/{id}', 'EventUersController@event_qr_details');
Route::get('not-attend-event-details/{id}', 'EventUersController@not_attend_event_details');
Route::get('hold-event-details/{id}', 'EventUersController@hold_event_details');
Route::get('failed-event-details/{id}', 'EventUersController@failed_event_details');
Route::get('non-attendance-event-details/{id}', 'EventUersController@non_attendance_event_details');

Route::get('confirmed-event-details/{id}', 'EventUersController@confirmed_event_details');
Route::get('confirmed-users-web-chat/{id}', 'EventUersController@confirmed_users_web_chat');


// import
Route::post('event-user-import', 'EventUersController@import');


Route::get('qr-sent-event-details/{id}', 'EventUersController@qr_sent_event_details');
Route::get('congratulations-event-messages-details/{id}', 'EventUersController@congratulations_event_messages_details');
Route::get('delete-event-messages/{id}/{type}', 'EventUersController@delete_event_messages');


Route::get('event-messages/{id}', 'EventUersController@event_messages');
Route::get('event-chat/{id}', 'EventUersController@event_chat_details');


Route::post('send-custom-message', 'EventUersController@send_custom_message');
Route::post('delete_event_users', 'EventUersController@delete_event_users');

Route::post('send-congratulation-message', 'EventUersController@send_congratulation_message');
Route::post('send-congratulation-messages', 'EventUersController@send_congratulation_messages');

Route::post('send-apologize-message', 'EventUersController@send_apologize_message');

Route::post('remember-users-to-event', 'EventUersController@remember_users_to_event');

Route::post('delete_selected_event_users', 'EventUersController@delete_selected_event_users');

Route::post('delete-messages', 'EventUersController@delete_messages');

Route::post('update-user-mobile', 'EventUersController@update_user_mobile');

Route::post('new-send-event-invitation', 'EventUersController@new_send_event_invitation');



// save_event_family
Route::get('save_event_family', function() {
    return redirect('admin');
});
Route::post('save_event_family', 'EventUersController@save_event_family');


// update_event_family
Route::get('update_event_family', function () {
    return redirect('admin');
});
Route::post('update_event_family', 'EventUersController@update_event_family');


// event_family_search
Route::get('event_family_search', 'EventUersController@event_family_search');


// destroy
Route::get('event_family/destroy/{id}', 'EventUersController@delete_event_family');


Route::get('open_event_family/{id}', 'EventUersController@open_event_family');




// login-user
Route::get('login-user/{id}', 'EventUersController@login_user');


// send-event-location
Route::get('send-event-location/{id}', 'EventUersController@send_event_location');


// event-report
Route::get('event-report/{id}', 'EventUersController@event_report');


// send-congratulations
Route::get('send-congratulations/{id}', 'EventUersController@send_congratulations');



// save-order
Route::get('save-order', function () {
    return redirect('admin');
});
Route::post('save-order', 'UsersController@save_order');


// edit-order
Route::get('edit-order', function () {
    return redirect('admin');
});
Route::post('edit-order', 'UsersController@edit_order');


Route::post('delete_selected_users', 'UsersController@delete_selected_users');



// mobile_codes
Route::resource('mobile_codes','MobileCodesController',['names' => 'admin.mobile_codes']);
Route::get('mobile_codes/destroy/{id}', 'MobileCodesController@destroy');


Route::get('messages', 'MessageController@contact_messages');
Route::get('messages/destroy/{id}', 'MessageController@delete_message');
Route::get('messages/seen/{id}', 'MessageController@seen1');


 // Subscribers

 Route::get('subscribers', 'SubscribersController@subscribers');
 Route::get('subscribers/destroy/{id}', 'SubscribersController@delete_subscriber');
 Route::get('subscribers/seen/{id}', 'SubscribersController@seen');



// reservation
Route::resource('reservation','ReservationController',[
    'names' => 'admin.reservation',
    'except' => ['update'],
]);
Route::post('reservation/{id}', 'ReservationController@update');
Route::get('reservation/destroy/{id}', 'ReservationController@destroy');
Route::post('send_reservation_to_paid', 'ReservationController@send_reservation_to_paid');
Route::post('send_reservation_info_to_user', 'ReservationController@send_reservation_info_to_user');


 
// Uses
Route::resource('custom_events', 'CustomEventController', [
    'names' => 'admin.custom_events',
    'except' => ['update'],
]);
Route::post('custom_events/{id}', 'CustomEventController@update');
Route::get('custom_events/destroy/{id}', 'CustomEventController@destroy');


Route::get('custom_events/{id}/event-visitors', 'CustomEventController@event_visitors');
Route::get('custom_events/{id}/send-events', 'CustomEventController@send_events');
Route::get('custom_events/{id}/users', 'CustomEventController@users');
Route::get('custom_events/{id}/event-report', 'CustomEventController@event_report');
Route::get('custom_events/{id}/event-users', 'CustomEventController@event_users');
Route::get('custom_events/{id}/enter-event', 'CustomEventController@enter_event');

Route::post('new-send-custom-event-invitation', 'CustomEventController@new_send_event_invitation');

Route::post('delete_selected_custom_event_users', 'CustomEventController@delete_selected_event_users');



// custom_event_users_search
Route::get('custom_event_users_search', 'CustomEventController@event_users_search');



// save_event_users
Route::get('save_custom_event_users', function() {
    return redirect('admin');
});
Route::post('save_custom_event_users', 'CustomEventController@save_event_users');


// update_custom_event_users
Route::get('update_custom_event_users', function () {
    return redirect('admin');
});
Route::post('update_custom_event_users', 'CustomEventController@update_event_users');


// delete event_users
Route::get('custom_event_users/destroy/{id}', 'CustomEventController@delete_event_users');

// import
Route::post('custom-event-user-import', 'CustomEventController@import');



// save_event_family
Route::get('save_custom_event_family', function() {
    return redirect('admin');
});
Route::post('save_custom_event_family', 'CustomEventController@save_event_family');


// update_custom_event_family
Route::get('update_custom_event_family', function () {
    return redirect('admin');
});
Route::post('update_custom_event_family', 'CustomEventController@update_event_family');


// event_family_search
Route::get('custom_event_family_search', 'CustomEventController@event_family_search');


// destroy
Route::get('custom_event_family/destroy/{id}', 'CustomEventController@delete_event_family');


Route::get('open_custom_event_family/{id}', 'CustomEventController@open_event_family');
