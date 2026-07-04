<?php

namespace Tests\Feature;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_is_redirected_to_login()
    {
        $response = $this->get(route('todos.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_sees_only_their_todos()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Todo::factory()->count(3)->create(['user_id' => $user->id]);
        Todo::factory()->count(2)->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->get(route('todos.index'));

        $response->assertInertia(fn ($page) => $page
            ->component('Todos/Index')
            ->has('todos', 3)
        );
    }

    public function test_user_cannot_update_another_users_todo()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)
            ->patch(route('todos.update', $todo), ['title' => 'Hacked!']);

        $response->assertForbidden();
    }

    public function test_user_cannot_delete_another_users_todo()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)
            ->delete(route('todos.destroy', $todo));

        $response->assertForbidden();
    }

    public function test_user_can_update_own_todo()
    {
        $user = User::factory()->create();
        $todo = Todo::factory()->create([
            'user_id' => $user->id,
            'title' => 'Original',
        ]);

        $response = $this->actingAs($user)
            ->patch(route('todos.update', $todo), ['title' => 'Updated']);

        $response->assertRedirect(route('todos.index'));
        $this->assertEquals('Updated', $todo->fresh()->title);
    }
}
