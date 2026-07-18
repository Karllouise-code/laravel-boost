<?php

namespace Tests\Unit\Models;

use App\Models\Board;
use App\Models\Column;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ColumnTest extends TestCase
{
    use RefreshDatabase;

    public function test_column_belongs_to_board(): void
    {
        $user = User::factory()->create();
        $board = Board::factory()->create(['owner_id' => $user->id]);
        $column = Column::create([
            'board_id' => $board->id,
            'name' => 'To Do',
            'color' => '#6366f1',
            'position' => 0,
        ]);

        $this->assertInstanceOf(Board::class, $column->board);
        $this->assertEquals($board->id, $column->board->id);
    }

    public function test_column_has_many_todos(): void
    {
        $user = User::factory()->create();
        $board = Board::factory()->create(['owner_id' => $user->id]);
        $column = Column::create([
            'board_id' => $board->id,
            'name' => 'To Do',
            'color' => '#6366f1',
            'position' => 0,
        ]);
        $todo = Todo::factory()->create(['board_id' => $board->id, 'column_id' => $column->id]);

        $this->assertCount(1, $column->todos);
        $this->assertEquals($todo->id, $column->todos->first()->id);
    }
}
