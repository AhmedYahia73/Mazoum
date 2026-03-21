<?php

use Illuminate\Support\Facades\Route;
 
use Api\CustomEvent\PackageController; 
 

Route::controller(PackageController::class)
->prefix("package")->group(function () {
    Route::get('/', 'view');  

}); 