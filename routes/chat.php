<?php

use Illuminate\Support\Facades\Route;


// event login

Route::get('event/login', 'EventChatLoginController@login');
Route::post('event/login', 'EventChatLoginController@login_post')->name('event.login');
Route::get('event/logout', 'EventChatLoginController@logout')->name('event.logout');


Route::get('event-chat', 'EventChatController@event_chat');

Route::post('website/chat/save_event_action', 'EventChatController@save_event_action')->name('website.chat.save_event_action');



