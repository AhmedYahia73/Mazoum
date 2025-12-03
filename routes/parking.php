<?php

use Illuminate\Support\Facades\Route;

// Home

Route::get('/', 'AdminController@home');



// Setting

Route::get('setting', 'SettingController@setting');
Route::post('setting', 'SettingController@update_setting');


// parking
Route::resource('parking', 'ParkingController', ['names' => 'parking_panel.parking']);
Route::get('parking/destroy/{id}', 'ParkingController@destroy');

Route::get('parking/sent-message/{id}', 'ParkingController@sent_message');

Route::get('leave-parking', 'ParkingController@leave_parking');

Route::get('confirm-leave-parking/{id}', 'ParkingController@confirm_leave_parking');

Route::get('exit_parking/{id}', 'ParkingController@exit_parking');

Route::get('reports', 'ParkingController@reports');

Route::get('reports/destroy/{id}', 'ParkingController@destroy');

Route::get('delete_selected_items', 'ParkingController@delete_selected_items');

