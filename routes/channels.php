<?php

use Illuminate\Support\Facades\Broadcast;

// Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });
// routes/channels.php
// Elimina o comenta la definición de presencia para este canal

// Opcional: si quieres mantener algún control mínimo
Broadcast::channel('chat.{roomId}', function ($user, $roomId) {
    return true; // permite a cualquiera (incluso guests)
});
