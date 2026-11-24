<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

use App\Models\CustomChat;
/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('user-chat.{userId}', function ($user, $userId) {
    // 1. If user is the conversation owner
    if (!($user instanceof \App\Models\Admin) && (int) $user->id === (int) $userId) {
        return true;
    }

    // 2. If user is an admin
    if (isset($user->role) && $user->role === 'admin') {
        return true;
    }

    // 3. If user is an employee with access to this user
    if (isset($user->role) && $user->role === 'employee') {
        return app(\App\Services\UserChatService::class)->employeeCanAccessUser($user->id, (int) $userId);
    }

    return false;
});

Broadcast::channel('admin-user-chat', function ($user) {
    return isset($user->role) && in_array($user->role, ['admin', 'employee']);
});

