<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PresenceChannel;

class UserLeft implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $nick;
    public string $roomId;

    public function __construct(string $nick, string $roomId)
    {
        $this->nick = $nick;
        $this->roomId = $roomId;
    }

    public function broadcastOn(): PresenceChannel
    {
        return new PresenceChannel('chat.' . $this->roomId);
    }

    public function broadcastAs(): string
    {
        return 'UserLeft';
    }

    public function broadcastWith(): array
    {
        return [
            'nick' => $this->nick,
        ];
    }
}
