<?php

use Illuminate\Support\Facades\Route;

// admin login

Route::get('admin/login', 'AdminLogin@login');
Route::post('admin/login', 'AdminLogin@login_post')->name('admin.login');
Route::get('admin/logout', 'AdminLogin@logout')->name('admin.logout');



// parking login

Route::get('parking_panel/login', 'ParkingLogin@login');
Route::post('parking_panel/login', 'ParkingLogin@login_post')->name('parking_panel.login');
Route::get('parking_panel/logout', 'ParkingLogin@logout')->name('parking_panel.logout');


Route::get('test_send', 'ParkingLogin@test_send');




// assistant login

Route::get('assistant_panel/login', 'AssistantLogin@login');
Route::post('assistant_panel/login', 'AssistantLogin@login_post')->name('assistant_panel.login');
Route::get('assistant_panel/logout', 'AssistantLogin@logout')->name('assistant_panel.logout');


// member login

Route::get('member_panel/login', 'MemberLogin@login');
Route::post('member_panel/login', 'MemberLogin@login_post')->name('member_panel.login');
Route::get('member_panel/logout', 'MemberLogin@logout')->name('member_panel.logout');


