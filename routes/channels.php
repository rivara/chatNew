<?php
use Illuminate\Support\Facades\Broadcast;
Broadcast::channel('chat.{roomId}', function ($user, $roomId) {
    return ['id' => $user->id, 'nick' => $user->name];
});
