<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TodoDeleted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public int $id, public string $boardSlug) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('board.'.$this->boardSlug)];
    }

    public function broadcastAs(): string
    {
        return 'TodoDeleted';
    }

    public function broadcastWith(): array
    {
        return ['id' => $this->id, 'board_slug' => $this->boardSlug];
    }
}
