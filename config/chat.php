<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Chat Timing & Policy Settings
    |--------------------------------------------------------------------------
    */

    // Allowed window in minutes for users/employees to edit a sent message.
    // Admins (role === 'admin') have unlimited edit time.
    'edit_window_minutes' => (int) env('CHAT_EDIT_WINDOW_MINUTES', 15),

    // Allowed window in minutes for users/employees to delete a message for everyone.
    // Admins (role === 'admin') have unlimited delete-for-everyone time.
    'delete_everyone_window_minutes' => (int) env('CHAT_DELETE_EVERYONE_WINDOW_MINUTES', 60),
];
