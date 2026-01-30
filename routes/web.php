<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use Inertia\Inertia;
use App\Events\MessageSent;
use Illuminate\Http\Request;


Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');



Route::match(['get', 'post'], '/chat', [ChatController::class, 'index'])->name('chat');

Route::post('/messages', function (Request $request) {
    $request->validate([
        'nick' => 'required|string|max:20',
        'message' => 'required|string|max:500',
        'room_id' => 'required|string',
    ]);

    // Dispara el evento a todos menos al que envía
    broadcast(new MessageSent($request->nick, $request->message,  $request->room_id))->toOthers();

    // Devuelve respuesta JSON
     return response()->json(['status' => 'ok']);
});

Route::post('/chat/joined', [ChatController::class, 'joined']);
Route::post('/chat/left',   [ChatController::class, 'left']);
