<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
// Home

Route::get('/', 'AdminController@home');

Route::post('/send_message', [HomeController::class, 'sendMessage']);

Route::get('watts_chat', 'AdminWattsChatController@index');

Route::get('profile', 'ProfileController@profile');
Route::post('update_profile', 'ProfileController@update_profile');

Route::post('send_watt_message', 'AdminWattsChatController@sendMessage');

Route::get('manager/destroy/{id}', 'AdminController@destroy');

Route::post('event_memory', 'MemoryController@event_memory');
Route::post('event_user_memory', 'MemoryController@event_user_memory');
Route::post('custom_event_memory', 'MemoryController@custom_event_memory');
Route::post('custom_user_memory', 'MemoryController@custom_user_memory');

Route::post('attendance_report', 'AttendanceController@attendance_report');
Route::get('attendance/users_list', 'AttendanceController@users_list');
Route::get('attendance_list', 'AttendanceController@index');
Route::post('attendance/user_attendance', 'AttendanceController@user_attendance');
Route::post('employee_attend', 'AttendanceController@employee_attend');
Route::post('employee_departure', 'AttendanceController@employee_departure');
Route::post('attendance/multi_delete', 'AttendanceController@multi_delete');
Route::get('attendance/report', 'AttendanceController@attendance_report');
Route::resource(
    'attendance',
    'AttendanceController',
    ['names' => 'admin.attendance']
);

Route::post('event_host/custom_users', 'EventHostController@custom_users');
Route::get('event_host/{id}', 'EventHostController@index');
Route::get('event_host/custom/{id}', 'EventHostController@custom_index');
Route::get('event_host/item/{id}', 'EventHostController@show');
Route::post('event_host/users', 'EventHostController@users');
Route::post('event_host/excel_users', 'EventHostController@excel_users');
Route::post('event_host/report', 'EventHostController@report');
Route::resource(
    'event_host',
    'EventHostController',
    ['names' => 'admin.event_host']
);

Route::post('attendance/multi_delete', 'AttendanceDataController@multi_delete');
Route::resource(
    'attendance_data',
    'AttendanceDataController',
    ['names' => 'admin.attendance_data']
);
   
 
// Admins

Route::resource(
    'manager',
    'AdminController',
    ['names' => 'admin.manager']
);

// // Admin Update Password
// Route::patch('manager/update_password/{id}', 'AdminController@UpdatePass')->name('admin.manager.UpdatePass');

Route::get('manager/destroy/{id}', 'AdminController@destroy');
Route::post('manager/multi_delete', 'AdminController@multi_delete');


// Assistant

Route::resource(
    'assistant',
    'AssistantController',
    ['names' => 'admin.assistant']
);

Route::get('assistant/destroy/{id}', 'AssistantController@destroy');
Route::post('assistant/multi_delete', 'AssistantController@multi_delete');



// Setting
Route::get('setting', 'SettingController@setting');
Route::post('setting', 'SettingController@update_setting');


// Users
Route::resource('users','UsersController',['names' => 'admin.users']);
Route::get('users/destroy/{id}', 'UsersController@destroy');
Route::post('users/multi_delete', 'UsersController@multi_delete');
Route::get('user-invoice/destroy/{id}', 'UsersController@delete_invoice');


// currency
Route::resource('currency','CurrencyController',['names' => 'admin.currency']);
Route::get('currency/destroy/{id}', 'CurrencyController@destroy');
Route::post('currency/multi_delete', 'CurrencyController@multi_delete');

// Country
Route::resource('country', 'CountryController', ['names' => 'admin.country']);
Route::get('country/destroy/{id}', 'CountryController@destroy');
Route::post('country/multi_delete', 'CountryController@multi_delete');
Route::patch('country/change_status/{id}', 'CountryController@change_status');

// Phone Setting
Route::get('phone_setting/lists', 'PhoneSettingController@lists');
Route::resource('phone_setting', 'PhoneSettingController', ['names' => 'admin.phone_setting']);
Route::get('phone_setting/destroy/{id}', 'PhoneSettingController@destroy');
Route::post('phone_setting/multi_delete', 'PhoneSettingController@multi_delete');
Route::patch('phone_setting/change_status/{id}', 'PhoneSettingController@change_status');


// Packages
Route::resource('packages','PackagesController',['names' => 'admin.packages',
    'except' => ['update']]);
Route::get('packages/destroy/{id}', 'PackagesController@destroy');
Route::post('packages/multi_delete', 'PackagesController@multi_delete');
Route::post('packages/{id}', 'PackagesController@update')->name('admin.packages.update');

// Payment Method
Route::put('payment_method/status/{id}', 'PaymentMethodController@status')->name('admin.payment_method.status');
Route::post('payment_method/multi_delete', 'PaymentMethodController@multi_delete');
Route::post('payment_method/{id}', 'PaymentMethodController@update')->name('admin.payment_method.update');
Route::resource('payment_method','PaymentMethodController',['names' => 'admin.payment_method']);

// Negotation
Route::get('negotation', 'NegotaitionController@view')->name('admin.negotation.view');
Route::get('negotation/history', 'NegotaitionController@history')->name('admin.negotation.history');
Route::get('negotation/item/{id}', 'NegotaitionController@negotaition')->name('admin.negotation.item');
Route::post('negotation/status/{id}', 'NegotaitionController@status')->name('admin.negotation.status');
Route::delete('negotation/delete/{id}', 'NegotaitionController@delete')->name('admin.negotation.delete');

// Pricing
Route::resource('pricing','PricingController',['names' => 'admin.pricing']);
Route::get('pricing/destroy/{id}', 'PricingController@destroy');
Route::post('pricing/multi_delete', 'PricingController@multi_delete');


// Uses
Route::resource('uses', 'UsesController', ['names' => 'admin.uses']);
Route::get('uses/destroy/{id}', 'UsesController@destroy');
Route::post('uses/multi_delete', 'UsesController@multi_delete');


// Desgins
Route::resource('desgins', 'DesginsController', ['names' => 'admin.desgins']);
Route::get('desgins/destroy/{id}', 'DesginsController@destroy');
Route::post('desgins/multi_delete', 'DesginsController@multi_delete');
Route::get('desgins/show-pdf/{id}', 'DesginsController@show_pdf');


// Web Desgins
Route::resource('desgins', 'DesginsController', [
    'names' => 'admin.desgins',
    'except' => ['update'],
]);
Route::post('desgins/{id}', 'DesginsController@update');

Route::post('web_desgins/multi_delete', 'WebDesginsController@multi_delete');
Route::post('web_desgins/{id}', 'WebDesginsController@update');
Route::get('web_desgins/destroy/{id}', 'WebDesginsController@destroy');
Route::get('web_desgins/show-pdf/{id}', 'WebDesginsController@show_pdf');
Route::resource('web_desgins', 'WebDesginsController', [
    'names' => 'admin.web_desgins',
    'except' => ['update'],
]);

// Events  
Route::get('events/phones_lists', 'EventsController@phones_lists');
Route::get('events/all_events', 'EventsController@all_events');
Route::get('events/all_current_events', 'EventsController@all_current_events');
Route::get('events/all_closed_events', 'EventsController@all_closed_events');
Route::get('events/all_deleted_events', 'EventsController@all_deleted_events');
Route::post('events/sendNewMessage', 'EventsController@sendNewMessage');
Route::post('events/delete_items', 'EventsController@delete_items');
Route::post('events/multi_delete', 'EventsController@multi_delete');
Route::get('events/event_lists', 'EventsController@event_lists');
Route::post('events/update_location', 'EventsController@update_location');
Route::delete('events/delete_event/{id}', 'EventsController@delete_event');
Route::get('events/assistant_lists', 'EventsController@assistant_lists');
Route::get('events/users_lists', 'EventsController@users_lists');
Route::resource('events', 'EventsController', [
    'names' => 'admin.events',
    'except' => ['update'],
]);

Route::post('events/{id}', 'EventsController@update');
Route::get('sa-events', 'EventsController@sa_events');

Route::get('events/phones_lists', 'EventsController@phones_lists');
Route::get('events/all_events', 'EventsController@all_events');
Route::get('events/all_current_events', 'EventsController@all_current_events');
Route::get('events/all_closed_events', 'EventsController@all_closed_events');
Route::get('events/all_deleted_events', 'EventsController@all_deleted_events');


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
Route::get('events/{id}/all_send_events', 'EventsController@all_send_events');
Route::get('events/{id}/event-report', 'EventsController@event_report');
Route::get('events/{id}/event-users', 'EventsController@event_users');
Route::get('events/{id}/event-location', 'EventsController@event_location');
Route::get('events/{id}/enter-event', 'EventsController@enter_event');
Route::get('events/{id}/scanner', 'EventsController@scanner');
Route::get('events/{id}/my-package', 'EventsController@my_package');
Route::put('events/{id}/update_my_package', 'EventsController@update_my_package');
Route::get('events/{id}/chat-list', 'EventsController@chat_list');


// 2222222  
Route::post('/send_congratulation_messages', 'CustomEventController@send_congratulation_messages');
Route::get('custom_events/my_package/{id}', 'CustomEventController@my_package');
Route::post('custom_events/send_custom_message', 'CustomEventController@send_custom_message');
Route::post('custom_events/re_send_custom_qr', 'CustomEventController@re_send_custom_qr');
Route::post('custom_events/scan_data', 'CustomEventController@scan_data');
Route::post('custom_events/scan_qr', 'CustomEventController@scan_qr');
Route::post('custom_events/excel_event_host_visitor', 'CustomEventController@excel_event_host_visitor');
Route::post('custom_events/excel_event_host_qr', 'CustomEventController@excel_event_host_qr');
Route::post('custom_events/excel_event_host_congrate_msg', 'CustomEventController@excel_event_host_congrate_msg');
Route::post('custom_events/excel_event_host_apologize_msg', 'CustomEventController@excel_event_host_apologize_msg');
Route::post('custom_events/excel_event_host_apologize', 'CustomEventController@excel_event_host_apologize');
Route::post('custom_events/excel_event_host_confirm', 'CustomEventController@excel_event_host_confirm');

Route::post('custom_events/event_host_report', 'CustomEventController@event_host_report');
Route::post('custom_events/event_host_visitor', 'CustomEventController@event_host_visitor');
Route::post('custom_events/event_host_qr', 'CustomEventController@event_host_qr');
Route::post('custom_events/event_host_congrate_msg', 'CustomEventController@event_host_congrate_msg');
Route::post('custom_events/event_host_apologize_msg', 'CustomEventController@event_host_apologize_msg');
Route::post('custom_events/event_host_apologize', 'CustomEventController@event_host_apologize');
Route::post('custom_events/event_host_confirm', 'CustomEventController@event_host_confirm');

Route::get('custom_events/send_event_location/{id}', 'CustomEventController@send_event_location');
Route::delete('custom_events/delete_congratulation_msg', 'CustomEventController@delete_messages');
Route::delete('custom_events/delete_apologize_msg', 'CustomEventController@delete_messages');
// delete_events
Route::post('delete_events', 'EventsController@delete_events');


// Event Users

// ---------------------------------------------
// save_event_users
Route::get('save_event_users', function() {
    return redirect('admin');
});
Route::get('event_open_users/{id}', 'EventUersController@event_open_users');
Route::get('event_open_users/{id}', 'EventUersController@event_open_users');
Route::post('events/sendWedingMsg', 'EventUersController@sendWedingMsg');
Route::post('save_event_users', 'EventUersController@save_event_users');
Route::post('sendMessageFashalTemplate', 'EventUersController@sendMessageFashalTemplate');
Route::post('user_save_event_users', 'EventUersController@user_save_event_users');

Route::get('faild_users/{id}', 'EventUersController@faild_users');
Route::post('send_invite_utility_msg', 'EventUersController@send_invite_utility_msg');


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
Route::post('send-qr/{id}', 'EventUersController@send_qr');
Route::get('send-new-qr/{id}', 'EventUersController@send_new_qr');
Route::post('send-new-qr/{id}', 'EventUersController@send_new_qr');


Route::get('accept-user-event/{id}', 'EventUersController@accept_user_event');
Route::get('refuse-user-event/{id}', 'EventUersController@refuse_user_event');


Route::get('qr-is-send/{id}', 'EventUersController@qr_is_send');
Route::get('is-send-event/{id}', 'EventUersController@is_send_event');



Route::get('all-invited-users/{id}', 'EventUersController@all_invited_users');
Route::get('excel-all-invited-users/{id}', 'EventUersController@excel_all_invited_users');
Route::get('event-qr-details/{id}', 'EventUersController@event_qr_details');
Route::get('excel-event-qr-details/{id}', 'EventUersController@excel_event_qr_details');
Route::get('not-attend-event-details/{id}', 'EventUersController@not_attend_event_details');
Route::get('excel-not-attend-event-details/{id}', 'EventUersController@excel_not_attend_event_details');
Route::get('hold-event-details/{id}', 'EventUersController@hold_event_details');
Route::get('excel-hold-event-details/{id}', 'EventUersController@excel_hold_event_details');
Route::get('is_remember/{id}', 'EventUersController@is_remember');
Route::get('excel_is_remember/{id}', 'EventUersController@excel_is_remember');
Route::get('failed-event-details/{id}', 'EventUersController@failed_event_details');
Route::get('excel-failed-event-details/{id}', 'EventUersController@excel_failed_event_details');
Route::get('non-attendance-event-details/{id}', 'EventUersController@non_attendance_event_details');
Route::get('excel-non-attendance-event-details/{id}', 'EventUersController@excel_non_attendance_event_details');

Route::get('confirmed-event-details/{id}', 'EventUersController@confirmed_event_details');
Route::get('excel-confirmed-event-details/{id}', 'EventUersController@excel_confirmed_event_details');
Route::get('confirmed-users-web-chat/{id}', 'EventUersController@confirmed_users_web_chat');
Route::get('excel-confirmed-users-web-chat/{id}', 'EventUersController@excel_confirmed_users_web_chat');


// import 
Route::post('event-user-import', 'EventUersController@import');
Route::post('import_users', 'EventUersController@import_users');


Route::get('qr-sent-event-details/{id}', 'EventUersController@qr_sent_event_details');
Route::get('excel-qr-sent-event-details/{id}', 'EventUersController@excel_qr_sent_event_details');
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
Route::get('event_family/{id}', 'EventUersController@event_family');


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
Route::post('scan_data', 'EventUersController@scan_data');
Route::post('scan_qr', 'EventUersController@scan_qr');


// send-event-location
Route::get('send-event-location/{id}', 'EventUersController@send_event_location');
Route::post('send-event-location/{id}', 'EventUersController@send_event_location');


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
Route::post('edit-order', 'UsersController@edit_order');


Route::post('delete_selected_users', 'UsersController@delete_selected_users');



// mobile_codes
Route::resource('mobile_codes','MobileCodesController',['names' => 'admin.mobile_codes']);
Route::get('mobile_codes/destroy/{id}', 'MobileCodesController@destroy');
Route::post('mobile_codes/multi_delete', 'MobileCodesController@multi_delete');


Route::get('messages', 'MessageController@contact_messages');
Route::get('messages/destroy/{id}', 'MessageController@delete_message');
Route::post('messages/multi_delete', 'MessageController@multi_delete');
Route::get('messages/seen/{id}', 'MessageController@seen1');


 // Subscribers

 Route::get('subscribers', 'SubscribersController@subscribers');
 Route::get('subscribers/destroy/{id}', 'SubscribersController@delete_subscriber');
 Route::post('subscribers/multi_delete', 'SubscribersController@multi_delete');
 Route::get('subscribers/seen/{id}', 'SubscribersController@seen');



// reservation
Route::resource('reservation','ReservationController',[
    'names' => 'admin.reservation',
    'except' => ['update'],
]);
Route::post('reservation/multi_delete', 'ReservationController@multi_delete');
Route::post('reservation/{id}', 'ReservationController@update');
Route::get('reservation/destroy/{id}', 'ReservationController@destroy');
Route::post('send_reservation_to_paid', 'ReservationController@send_reservation_to_paid');
Route::post('send_reservation_info_to_user', 'ReservationController@send_reservation_info_to_user');


 
// Uses  
Route::post('custom_events/excel_event_users', 'CustomEventController@excel_event_users');
Route::post('custom_events/excel_event_family', 'CustomEventController@excel_event_family');
Route::get('custom_events/excel_confirm_count/{id}', 'CustomEventController@excel_confirm_count');
Route::get('custom_events/excel_apologize_count/{id}', 'CustomEventController@excel_apologize_count');

Route::get('custom_events/qr_count/{id}', 'CustomEventController@qr_count');
Route::get('custom_events/excel_qr_count/{id}', 'CustomEventController@excel_qr_count');
Route::post('custom_events/remember_users_to_event', 'CustomEventController@remember_users_to_event');
Route::get('custom_events/restore_deleted/{id}', 'CustomEventController@restore_deleted');
Route::get('custom_events/deleted_custom_events', 'CustomEventController@deleted_custom_events');
Route::resource('custom_events', 'CustomEventController', [
    'names' => 'admin.custom_events',
    'except' => ['update'],
]);

Route::post('custom_events/save_host_event_users', 'CustomEventController@save_host_event_users');
Route::delete('custom_events/force_destroy/{id}', 'CustomEventController@force_destroy');
Route::post('custom_events/force_multi_delete', 'CustomEventController@force_multi_delete');
Route::get('custom_events/confirm_count/{id}', 'CustomEventController@confirm_count');
Route::get('custom_events/apologize_count/{id}', 'CustomEventController@apologize_count');
Route::post('custom_events/host_custom_create', 'CustomEventController@host_custom_create');
Route::put('custom_events/host_custom_update/{id}', 'CustomEventController@host_custom_update');
Route::get('custom_events/host_custom_event/{id}', 'CustomEventController@host_custom_event');
Route::get('custom_events/congratulation_msg/{id}', 'CustomEventController@congratulation_msg');
Route::get('custom_events/apologize_msg/{id}', 'CustomEventController@apologize_msg');
Route::post('custom_events/send_message', 'CustomEventController@send_message');
Route::put('custom_events/status/{id}', 'CustomEventController@status');
Route::post('new-send-custom-event-invitation', 'CustomEventController@new_send_event_invitation');

Route::get('custom_open_users/{id}', 'CustomEventController@custom_open_users');
Route::post('custom_events/multi_delete', 'CustomEventController@multi_delete');
Route::post('custom_events/{id}', 'CustomEventController@update');
Route::get('custom_events/destroy/{id}', 'CustomEventController@destroy');


Route::get('custom_events/{id}/event-visitors', 'CustomEventController@event_visitors');
Route::get('custom_events/{id}/send-events', 'CustomEventController@send_events');
Route::get('custom_events/{id}/users', 'CustomEventController@users');
Route::get('custom_events/{id}/event-report', 'CustomEventController@event_report');
Route::get('custom_events/{id}/event-users', 'CustomEventController@event_users');
Route::get('custom_events/{id}/all_event_users', 'CustomEventController@all_event_users');
Route::get('custom_events/{id}/enter-event', 'CustomEventController@enter_event');


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


// Attendance Data
Route::resource('attendance_data', 'AttendanceDataController', [
    'names' => 'admin.attendance_data'
]);
Route::post('attendance_data/multi_delete', 'AttendanceDataController@multi_delete');

// Attendance
Route::get('attendance/users_list', 'AttendanceController@users_list');
Route::resource('attendance', 'AttendanceController', [
    'names' => 'admin.attendance',
]);
Route::post('attendance/multi_delete', 'AttendanceController@multi_delete');

