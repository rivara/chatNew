<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Events\UserJoined;
use App\Events\UserLeft;
class ChatController extends Controller
{

    public function index(Request $request)
    {
        // Validación simple
        $request->validate([
            'nick' => ['required', 'string', 'max:20'],
            'room' => ['required', 'string', 'max:50'],
        ]);

        return Inertia::render('Chat', [
            'nick' => $request->nick,
        ]);
    }

public function joined(Request $request)
    {
        $request->validate([
            'nick'    => 'required|string|max:50',
            'room_id' => 'required|string',
        ]);

        $nick   = $request->nick;
        $roomId = $request->room_id;

        broadcast(new UserJoined($nick, $roomId))->toOthers();

        return response()->json(['status' => 'joined']);
    }

public function left(Request $request)
    {
        $request->validate([
            'nick'    => 'required|string|max:50',
            'room_id' => 'required|string',
        ]);

        $nick   = $request->nick;
        $roomId = $request->room_id;

        broadcast(new UserLeft($nick, $roomId))->toOthers();

        return response()->json(['status' => 'left']);
    }



}
