<?php

use Illuminate\Support\Facades\Route;

// Home

Route::get('/', 'DashboardController@home');

Route::get('event-messages/{id}', 'DashboardController@event_messages');
