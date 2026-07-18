<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Todo extends Model
{
    /** @use HasFactory<\Database\Factories\TodoFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'completed',
        'priority',
        'due_date',
        'column_id',
        'board_id',
    ];

    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }

    public function column(): BelongsTo
    {
        return $this->belongsTo(Column::class);
    }

    protected function casts(): array
    {
        return [
            'completed' => 'boolean',
            'priority' => 'integer',
            'due_date' => 'date',
        ];
    }
}
