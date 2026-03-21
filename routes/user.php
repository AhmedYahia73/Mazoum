<?php

use Illuminate\Support\Facades\Route;

use Api\CustomEvent\ChatController;
use Api\CustomEvent\PackageController;
use Api\CustomEvent\UserController;

Route::controller(ChatController::class)
->prefix("chat")->group(function () {

});

Route::controller(PackageController::class)
->prefix("package")->group(function () {
    Route::get('/', 'view');  

});

Route::controller(UserController::class)
->prefix("user")->group(function () {
    Route::get('/', 'view');    
    Route::get('/lists', 'lists');    
    Route::post('/add', 'create');    

});