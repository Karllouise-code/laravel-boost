<?php

namespace App\Events;

use App\Models\Column;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ColumnDeleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Column $column) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('board.'.$this->column->board->slug)];
    }

    public function broadcastAs(): string
    {
        return 'ColumnDeleted';
    }

    public function broadcastWith(): array
    {
        return ['id' => $this->column->id];
    }
}
