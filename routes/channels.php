<?php
use Illuminate\Support\Facades\Broadcast;

// Broadcast::channel('chat.{roomId}', function ($user, $roomId) {
//     $nick = session('nick');

//     if (!$nick) {
//         return false; // NO entra al channel
//     }

//     return [
//         'id'   => uniqid(),
//         'nick' => $nick,
//     ];
// });


// Broadcast::channel('chat.{roomId}', function ($user, $roomId) {
//     return [
//         'id' => $user->id ?? uniqid(),
//         'nick' => $user->nick ?? request('nick'),
//     ];
// });
