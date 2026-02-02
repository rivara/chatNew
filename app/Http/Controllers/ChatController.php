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

    // public function joined(Request $request)
    // {
    //     $request->validate([
    //         'nick'   => 'required|string|max:20',
    //         'roomId' => 'required|string|max:50',
    //     ]);

    //     $roomId = $request->roomId;
    //     $nick = $request->nick;

    //     $users = Cache::get("chat:$roomId", []);

    //     if (!in_array($nick, $users)) {
    //         $users[] = $nick;
    //         Cache::put("chat:$roomId", $users);
    //     }

    //     broadcast(new UserJoined($roomId, $nick))->toOthers();

    //     return response()->json([
    //         'onlineUsers' => $users,
    //     ]);
    // }

    // public function left(Request $request)
    // {
    //     $request->validate([
    //         'nick'   => 'required|string|max:20',
    //         'roomId' => 'required|string|max:50',
    //     ]);

    //     $roomId = $request->roomId;
    //     $nick = $request->nick;

    //     $users = Cache::get("chat:$roomId", []);
    //     $users = array_values(array_filter($users, fn ($u) => $u !== $nick));

    //     Cache::put("chat:$roomId", $users);

    //     broadcast(new UserLeft($roomId, $nick))->toOthers();

    //     return response()->json(['status' => 'left']);
    // }






// public function store(Request $request)
// {
//     $request->validate([
//         'nick'   => 'required|string|max:20',
//         'message'=> 'required|string|max:500',
//         'roomId' => 'required|string|max:50',
//     ]);

//     broadcast(
//         new MessageSent(
//             $request->nick,
//             $request->message,
//             $request->roomId
//         )
//     );

//     return response()->json(['status' => 'sent']);
// }


 public function store(Request $request)
    {
        $validated = $request->validate([
            'nick'    => 'required|string|max:20',
            'message' => 'required|string|max:500',
            'roomId'  => 'required|string|max:50',
        ]);

        //CLAVE: broadcast SIN toOthers() para que TODOS lo vean
        broadcast(new MessageSent(
            $validated['nick'],
            $validated['message'],
            $validated['roomId']
        ));

        return response()->json([
            'status' => 'sent',
        ]);
    }










}
