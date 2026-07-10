<?php

namespace Tests\Feature;

use App\Models\Board;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BoardTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_sees_their_boards()
    {
        $user = User::factory()->create();
        $board = Board::factory()->create(['owner_id' => $user->id]);
        $board->collaborators()->attach($user->id);

        $response = $this->actingAs($user)->get(route('boards.index'));

        $response->assertInertia(fn ($page) => $page
            ->component('Boards/Index')
            ->has('boards', 1)
        );
    }

    public function test_user_can_create_board()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('boards.store'), [
            'name' => 'Project Alpha',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('boards', [
            'name' => 'Project Alpha',
            'owner_id' => $user->id,
        ]);
    }

    public function test_user_can_update_board_name()
    {
        $user = User::factory()->create();
        $board = Board::factory()->create(['owner_id' => $user->id]);
        $board->collaborators()->attach($user->id);

        $response = $this->actingAs($user)
            ->patch(route('boards.update', $board->slug), ['name' => 'Renamed']);

        $response->assertRedirect();
        $this->assertEquals('Renamed', $board->fresh()->name);
    }

    public function test_owner_can_delete_board()
    {
        $user = User::factory()->create();
        $board = Board::factory()->create(['owner_id' => $user->id]);
        $board->collaborators()->attach($user->id);

        $response = $this->actingAs($user)
            ->delete(route('boards.destroy', $board->slug));

        $response->assertRedirect(route('boards.index'));
        $this->assertModelMissing($board);
    }

    public function test_collaborator_cannot_delete_board()
    {
        $owner = User::factory()->create();
        $collaborator = User::factory()->create();
        $board = Board::factory()->create(['owner_id' => $owner->id]);
        $board->collaborators()->attach([$owner->id, $collaborator->id]);

        $response = $this->actingAs($collaborator)
            ->delete(route('boards.destroy', $board->slug));

        $response->assertForbidden();
    }

    public function test_owner_can_add_collaborator()
    {
        $owner = User::factory()->create();
        $newUser = User::factory()->create();
        $board = Board::factory()->create(['owner_id' => $owner->id]);
        $board->collaborators()->attach($owner->id);

        $response = $this->actingAs($owner)
            ->post(route('boards.collaborators.store', $board->slug), [
                'email' => $newUser->email,
            ]);

        $response->assertSessionHas('message');
        $this->assertTrue($board->fresh()->collaborators->contains($newUser->id));
    }

    public function test_collaborator_cannot_add_collaborator()
    {
        $owner = User::factory()->create();
        $collaborator = User::factory()->create();
        $newUser = User::factory()->create();
        $board = Board::factory()->create(['owner_id' => $owner->id]);
        $board->collaborators()->attach([$owner->id, $collaborator->id]);

        $response = $this->actingAs($collaborator)
            ->post(route('boards.collaborators.store', $board->slug), [
                'email' => $newUser->email,
            ]);

        $response->assertForbidden();
    }
}
