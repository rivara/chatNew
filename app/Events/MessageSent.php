<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PresenceChannel;

// class MessageSent implements ShouldBroadcast
// {
//     use Dispatchable, InteractsWithSockets, SerializesModels;

//     public function __construct(
//         public string $nick,
//         public string $message,
//         public string $roomId
//     ) {}

//     public function broadcastOn(): Channel
//     {
//         return new Channel('chat.' . $this->roomId);
//     }

//     public function broadcastAs(): string
//     {
//         return 'MessageSent';
//     }

//     public function broadcastWith(): array
//     {
//         return [
//             'nick' => $this->nick,
//             'message' => $this->message,
//         ];
//     }
// }


class MessageSent implements ShouldBroadcast
{
    public function __construct(
        public string $nick,
        public string $message,
        public string $roomId
    ) {}

    public function broadcastOn()
    {
        return new Channel('chat.' . $this->roomId);
    }

    public function broadcastAs()
    {
        return 'MessageSent';
    }
}
