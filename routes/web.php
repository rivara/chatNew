<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use Inertia\Inertia;
use App\Events\MessageSent;
use Illuminate\Http\Request;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

// Ruta principal del chat (solo GET para mostrar la vista Inertia)
Route::get('/chat', [ChatController::class, 'index'])->name('chat');

// Ruta para enviar mensajes (POST)
Route::post('/messages', [ChatController::class, 'store'])->name('messages.store');

// Si en algún momento necesitas las rutas joined/left (opcional con presencia nativa)
Route::post('/chat/joined', [ChatController::class, 'joined'])->name('chat.joined');
Route::post('/chat/left',   [ChatController::class, 'left'])->name('chat.left');
