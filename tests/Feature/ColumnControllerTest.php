<?php

namespace Tests\Feature;

use App\Models\Board;
use App\Models\Column;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ColumnControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Board $board;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->board = Board::factory()->create(['owner_id' => $this->user->id]);
        $this->board->collaborators()->attach($this->user->id);
    }

    public function test_store_creates_column(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('columns.store', $this->board->slug), [
                'name' => 'Review',
                'color' => '#8b5cf6',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('columns', [
            'board_id' => $this->board->id,
            'name' => 'Review',
            'color' => '#8b5cf6',
        ]);
    }

    public function test_store_validates_max_10_columns(): void
    {
        for ($i = 0; $i < 10; $i++) {
            Column::create([
                'board_id' => $this->board->id,
                'name' => "Column $i",
                'color' => '#6366f1',
                'position' => $i,
            ]);
        }

        $response = $this->actingAs($this->user)
            ->post(route('columns.store', $this->board->slug), [
                'name' => 'Column 11',
                'color' => '#6366f1',
            ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_update_modifies_column(): void
    {
        $column = Column::create([
            'board_id' => $this->board->id,
            'name' => 'Old Name',
            'color' => '#6366f1',
            'position' => 0,
        ]);

        $response = $this->actingAs($this->user)
            ->patch(route('columns.update', [$this->board->slug, $column->id]), [
                'name' => 'New Name',
                'color' => '#10b981',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('columns', [
            'id' => $column->id,
            'name' => 'New Name',
            'color' => '#10b981',
        ]);
    }

    public function test_destroy_moves_todos_and_deletes_column(): void
    {
        $col1 = Column::create([
            'board_id' => $this->board->id,
            'name' => 'To Do',
            'color' => '#6366f1',
            'position' => 0,
        ]);
        $col2 = Column::create([
            'board_id' => $this->board->id,
            'name' => 'Done',
            'color' => '#10b981',
            'position' => 1,
        ]);
        $todo = \App\Models\Todo::factory()->create([
            'board_id' => $this->board->id,
            'column_id' => $col1->id,
        ]);

        $response = $this->actingAs($this->user)
            ->delete(route('columns.destroy', [$this->board->slug, $col1->id]));

        $response->assertRedirect();
        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'column_id' => $col2->id,
        ]);
        $this->assertDatabaseMissing('columns', ['id' => $col1->id]);
    }

    public function test_destroy_prevents_deleting_last_column(): void
    {
        $column = Column::create([
            'board_id' => $this->board->id,
            'name' => 'Only Column',
            'color' => '#6366f1',
            'position' => 0,
        ]);

        $response = $this->actingAs($this->user)
            ->delete(route('columns.destroy', [$this->board->slug, $column->id]));

        $response->assertSessionHasErrors('name');
        $this->assertDatabaseHas('columns', ['id' => $column->id]);
    }

    public function test_reorder_updates_positions(): void
    {
        $col1 = Column::create([
            'board_id' => $this->board->id,
            'name' => 'First',
            'color' => '#6366f1',
            'position' => 0,
        ]);
        $col2 = Column::create([
            'board_id' => $this->board->id,
            'name' => 'Second',
            'color' => '#10b981',
            'position' => 1,
        ]);

        $response = $this->actingAs($this->user)
            ->patch(route('columns.reorder', $this->board->slug), [
                'columns' => [
                    ['id' => $col2->id, 'position' => 0],
                    ['id' => $col1->id, 'position' => 1],
                ],
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('columns', ['id' => $col2->id, 'position' => 0]);
        $this->assertDatabaseHas('columns', ['id' => $col1->id, 'position' => 1]);
    }
}
