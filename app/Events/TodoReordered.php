<?php

namespace App\Events;

use App\Models\Todo;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TodoReordered implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Todo $todo) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('board.'.$this->todo->board->slug)];
    }

    public function broadcastAs(): string
    {
        return 'TodoReordered';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->todo->id,
            'status' => $this->todo->status,
            'priority' => $this->todo->priority,
            'board_slug' => $this->todo->board->slug,
        ];
    }
}
