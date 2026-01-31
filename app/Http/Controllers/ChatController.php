<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Events\UserJoined;
use App\Events\UserLeft;
use Illuminate\Support\Facades\Cache;
use App\Events\MessageSent;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'nick'   => ['required', 'string', 'max:20'],
            'roomId' => ['required', 'string', 'max:50'],
        ]);

        return Inertia::render('Chat', [
            'nick'   => $request->nick,
            'roomId' => $request->roomId,
        ]);
    }

    public function joined(Request $request)
    {
        $request->validate([
            'nick'   => 'required|string|max:20',
            'roomId' => 'required|string|max:50',
        ]);

        $room = $request->roomId;
        $nick = $request->nick;

        $users = Cache::get("chat:$room", []);

        if (!in_array($nick, $users)) {
            $users[] = $nick;
            Cache::put("chat:$room", $users);
        }

        broadcast(new UserJoined($room, $nick))->toOthers();

        return response()->json([
            'onlineUsers' => $users,
        ]);
    }

    public function left(Request $request)
    {
        $request->validate([
            'nick'   => 'required|string|max:20',
            'roomId' => 'required|string|max:50',
        ]);

        $room = $request->roomId;
        $nick = $request->nick;

        $users = Cache::get("chat:$room", []);
        $users = array_values(array_filter($users, fn ($u) => $u !== $nick));

        Cache::put("chat:$room", $users);

        broadcast(new UserLeft($room, $nick))->toOthers();

        return response()->json(['status' => 'left']);
    }






public function store(Request $request)
{
    $request->validate([
        'nick'   => 'required|string|max:20',
        'message'=> 'required|string|max:500',
        'roomId' => 'required|string|max:50',
    ]);

    broadcast(
        new MessageSent(
            $request->roomId,
            $request->nick,
            $request->message
        )
    )->toOthers();

    return response()->json(['status' => 'sent']);
}









}
