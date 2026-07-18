<?php

namespace App\Events;

use App\Models\Board;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ColumnsReordered implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Board $board, public array $columns) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('board.'.$this->board->slug)];
    }

    public function broadcastAs(): string
    {
        return 'ColumnsReordered';
    }

    public function broadcastWith(): array
    {
        return ['columns' => $this->columns];
    }
}
