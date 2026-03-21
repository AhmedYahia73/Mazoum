<?php

use Illuminate\Support\Facades\Route;


Route::controller('Api\CustomEvent\ChatController')
->prefix("chat")->group(function () {

});

Route::controller('Api\CustomEvent\PackageController')
->prefix("package")->group(function () {
    Route::get('/', 'view');
    Route::post('/payment', 'payment');
    // ____________________________________
    Route::post('/save_event_users', 'save_event_users');
    Route::get('/event_visitors/{id}', 'event_visitors');
    Route::post('/update_event_users', 'update_event_users');
    Route::delete('/destroy_user/{id}', 'destroy_user');
    //______________________________________________
    Route::get('/send_invitations', 'send_invitations');
    Route::post('/new_send_event_invitation', 'new_send_event_invitation');
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
    Route::get('/lists', 'lists');    
    Route::post('/add', 'create');    

});