<?php

namespace App\Events;

use App\Models\Board;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ColumnsReordered implements ShouldBroadcastNow
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
        return [
            'board_id' => $this->board->id,
            'columns' => $this->columns,
        ];
    }
}
