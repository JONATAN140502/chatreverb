<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Canal para chat privado
Broadcast::channel('chat.private.{userId1}.{userId2}', function ($user, $userId1, $userId2) {
    return (int) $user->id === (int) $userId1 || (int) $user->id === (int) $userId2;
});
