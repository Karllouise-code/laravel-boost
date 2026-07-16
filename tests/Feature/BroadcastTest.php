<?php

namespace Tests\Feature;

use App\Events\TodoCreated;
use App\Events\TodoDeleted;
use App\Events\TodoReordered;
use App\Events\TodoUpdated;
use App\Models\Board;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BroadcastTest extends TestCase
{
    use RefreshDatabase;

    public function test_todo_created_event_broadcasts_on_correct_channel()
    {
        $user = User::factory()->create();
        $board = Board::factory()->create(['owner_id' => $user->id]);
        $board->collaborators()->attach($user->id);
        $todo = Todo::factory()->create(['board_id' => $board->id]);

        $event = new TodoCreated($todo);

        $channels = $event->broadcastOn();
        $this->assertEquals('private-board.'.$board->slug, $channels[0]->name);
    }

    public function test_todo_updated_event_broadcasts_on_correct_channel()
    {
        $user = User::factory()->create();
        $board = Board::factory()->create(['owner_id' => $user->id]);
        $board->collaborators()->attach($user->id);
        $todo = Todo::factory()->create(['board_id' => $board->id]);

        $event = new TodoUpdated($todo);

        $channels = $event->broadcastOn();
        $this->assertEquals('private-board.'.$board->slug, $channels[0]->name);
    }

    public function test_todo_deleted_event_broadcasts_on_correct_channel()
    {
        $user = User::factory()->create();
        $board = Board::factory()->create(['owner_id' => $user->id]);
        $board->collaborators()->attach($user->id);

        $event = new TodoDeleted(1, $board->slug);

        $channels = $event->broadcastOn();
        $this->assertEquals('private-board.'.$board->slug, $channels[0]->name);
        $this->assertEquals(['id' => 1, 'board_slug' => $board->slug], $event->broadcastWith());
    }

    public function test_todo_reordered_event_broadcasts_on_correct_channel()
    {
        $user = User::factory()->create();
        $board = Board::factory()->create(['owner_id' => $user->id]);
        $board->collaborators()->attach($user->id);
        $todo = Todo::factory()->create(['board_id' => $board->id, 'status' => 'done', 'priority' => 1]);

        $event = new TodoReordered($todo);

        $channels = $event->broadcastOn();
        $this->assertEquals('private-board.'.$board->slug, $channels[0]->name);
        $this->assertEquals($todo->toArray(), $event->broadcastWith());
    }

    public function test_events_implement_should_broadcast()
    {
        $user = User::factory()->create();
        $board = Board::factory()->create(['owner_id' => $user->id]);
        $board->collaborators()->attach($user->id);
        $todo = Todo::factory()->create(['board_id' => $board->id]);

        $this->assertInstanceOf(\Illuminate\Contracts\Broadcasting\ShouldBroadcastNow::class, new TodoCreated($todo));
        $this->assertInstanceOf(\Illuminate\Contracts\Broadcasting\ShouldBroadcastNow::class, new TodoUpdated($todo));
        $this->assertInstanceOf(\Illuminate\Contracts\Broadcasting\ShouldBroadcastNow::class, new TodoReordered($todo));
        $this->assertInstanceOf(\Illuminate\Contracts\Broadcasting\ShouldBroadcastNow::class, new TodoDeleted($todo->id, $board->slug));
    }

    public function test_reorder_dispatches_event()
    {
        $user = User::factory()->create();
        $board = Board::factory()->create(['owner_id' => $user->id]);
        $board->collaborators()->attach($user->id);
        $todo = Todo::factory()->create(['board_id' => $board->id, 'status' => 'todo', 'priority' => 1]);

        $response = $this->actingAs($user)
            ->patchJson(route('todos.reorder', $board->slug), [
                'todo_id' => $todo->id,
                'status' => 'done',
                'priority' => 3,
            ]);

        $response->assertRedirect();
        $this->assertEquals('done', $todo->fresh()->status);
        $this->assertEquals(3, $todo->fresh()->priority);
    }

    public function test_reorder_validates_required_fields()
    {
        $user = User::factory()->create();
        $board = Board::factory()->create(['owner_id' => $user->id]);
        $board->collaborators()->attach($user->id);

        $response = $this->actingAs($user)
            ->patchJson(route('todos.reorder', $board->slug), []);

        $response->assertUnprocessable();
    }

    public function test_user_cannot_reorder_todo_on_board_they_have_no_access_to()
    {
        $owner = User::factory()->create();
        $stranger = User::factory()->create();
        $board = Board::factory()->create(['owner_id' => $owner->id]);
        $board->collaborators()->attach($owner->id);
        $todo = Todo::factory()->create(['board_id' => $board->id]);

        $response = $this->actingAs($stranger)
            ->patchJson(route('todos.reorder', $board->slug), [
                'todo_id' => $todo->id,
                'status' => 'done',
                'priority' => 1,
            ]);

        $response->assertForbidden();
    }
}
