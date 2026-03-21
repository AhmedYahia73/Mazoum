<?php

use Illuminate\Support\Facades\Route;

use Api\CustomEvent\ChatController;
use Api\CustomEvent\PackageController;
use Api\CustomEvent\UserController;
 

Route::controller(PackageController::class)
->prefix("package")->group(function () {
    Route::get('/', 'view');  

}); 