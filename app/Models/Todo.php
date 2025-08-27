<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    ];

    protected function casts(): array
    {
        return [
            'completed' => 'boolean',
            'priority' => 'integer',
            'due_date' => 'date',
        ];
    }
}
