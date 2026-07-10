<?php

namespace Tests\Feature;

use App\Models\Board;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SharedBoardTest extends TestCase
{
    use RefreshDatabase;

    public function test_logged_in_user_sees_join_page_for_unjoined_board()
    {
        $owner = User::factory()->create();
        $visitor = User::factory()->create();
        $board = Board::factory()->create(['owner_id' => $owner->id, 'slug' => 'abc12345']);
        $board->collaborators()->attach($owner->id);

        $response = $this->actingAs($visitor)
            ->get(route('shared.show', 'abc12345'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Shared/Access')
            ->has('board')
        );
    }

    public function test_collaborator_is_redirected_to_board()
    {
        $owner = User::factory()->create();
        $board = Board::factory()->create(['owner_id' => $owner->id, 'slug' => 'abc12345']);
        $board->collaborators()->attach($owner->id);

        $response = $this->actingAs($owner)
            ->get(route('shared.show', 'abc12345'));

        $response->assertRedirect(route('boards.show', 'abc12345'));
    }

    public function test_user_can_join_board_via_share_link()
    {
        $owner = User::factory()->create();
        $visitor = User::factory()->create();
        $board = Board::factory()->create(['owner_id' => $owner->id, 'slug' => 'abc12345']);
        $board->collaborators()->attach($owner->id);

        $response = $this->actingAs($visitor)
            ->post(route('shared.join', 'abc12345'));

        $response->assertRedirect(route('boards.show', 'abc12345'));
        $this->assertTrue($board->fresh()->collaborators->contains($visitor->id));
    }

    public function test_nonexistent_slug_returns_404()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('shared.show', 'nonexistent'));

        $response->assertNotFound();
    }
}
