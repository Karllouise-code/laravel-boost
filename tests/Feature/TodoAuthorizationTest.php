<?php

namespace Tests\Feature;

use App\Models\Board;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_is_redirected_to_login()
    {
        $board = Board::factory()->create();

        $response = $this->get(route('boards.show', $board->slug));

        $response->assertRedirect(route('login'));
    }

    public function test_user_can_view_board_they_own()
    {
        $user = User::factory()->create();
        $board = Board::factory()->create(['owner_id' => $user->id]);
        $board->collaborators()->attach($user->id);

        $response = $this->actingAs($user)->get(route('boards.show', $board->slug));

        $response->assertOk();
    }

    public function test_user_can_view_board_they_collaborate_on()
    {
        $owner = User::factory()->create();
        $collaborator = User::factory()->create();
        $board = Board::factory()->create(['owner_id' => $owner->id]);
        $board->collaborators()->attach([$owner->id, $collaborator->id]);

        $response = $this->actingAs($collaborator)->get(route('boards.show', $board->slug));

        $response->assertOk();
    }

    public function test_user_cannot_view_board_they_have_no_access_to()
    {
        $owner = User::factory()->create();
        $stranger = User::factory()->create();
        $board = Board::factory()->create(['owner_id' => $owner->id]);
        $board->collaborators()->attach($owner->id);

        $response = $this->actingAs($stranger)->get(route('boards.show', $board->slug));

        $response->assertForbidden();
    }

    public function test_collaborator_can_update_todo_on_shared_board()
    {
        $owner = User::factory()->create();
        $collaborator = User::factory()->create();
        $board = Board::factory()->create(['owner_id' => $owner->id]);
        $board->collaborators()->attach([$owner->id, $collaborator->id]);
        $todo = Todo::factory()->create(['board_id' => $board->id]);

        $response = $this->actingAs($collaborator)
            ->patch(route('todos.update', [$board->slug, $todo->id]), ['title' => 'Updated']);

        $response->assertRedirect();
        $this->assertEquals('Updated', $todo->fresh()->title);
    }

    public function test_collaborator_can_delete_todo_on_shared_board()
    {
        $owner = User::factory()->create();
        $collaborator = User::factory()->create();
        $board = Board::factory()->create(['owner_id' => $owner->id]);
        $board->collaborators()->attach([$owner->id, $collaborator->id]);
        $todo = Todo::factory()->create(['board_id' => $board->id]);

        $response = $this->actingAs($collaborator)
            ->delete(route('todos.destroy', [$board->slug, $todo->id]));

        $response->assertRedirect();
        $this->assertModelMissing($todo);
    }

    public function test_user_cannot_access_todo_on_board_they_have_no_access_to()
    {
        $owner = User::factory()->create();
        $stranger = User::factory()->create();
        $board = Board::factory()->create(['owner_id' => $owner->id]);
        $board->collaborators()->attach($owner->id);
        $todo = Todo::factory()->create(['board_id' => $board->id]);

        $response = $this->actingAs($stranger)
            ->patch(route('todos.update', [$board->slug, $todo->id]), ['title' => 'Hacked']);

        $response->assertForbidden();
    }
}
