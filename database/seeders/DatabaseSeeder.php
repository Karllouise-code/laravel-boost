<?php

namespace Database\Seeders;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate([
            'email' => 'test@example.com',
        ], [
            'name' => 'Test User',
            'password' => bcrypt('password'),
        ]);

        Todo::factory(12)->sequence(
            ['status' => 'todo', 'user_id' => $user->id],
            ['status' => 'todo', 'user_id' => $user->id],
            ['status' => 'todo', 'user_id' => $user->id],
            ['status' => 'todo', 'user_id' => $user->id],
            ['status' => 'in_progress', 'user_id' => $user->id],
            ['status' => 'in_progress', 'user_id' => $user->id],
            ['status' => 'in_progress', 'user_id' => $user->id],
            ['status' => 'in_progress', 'user_id' => $user->id],
            ['status' => 'done', 'user_id' => $user->id],
            ['status' => 'done', 'user_id' => $user->id],
            ['status' => 'done', 'user_id' => $user->id],
            ['status' => 'done', 'user_id' => $user->id],
        )->create();

        // Create a second user with fewer todos
        $user2 = User::firstOrCreate([
            'email' => 'user2@example.com',
        ], [
            'name' => 'Second User',
            'password' => bcrypt('password'),
        ]);

        Todo::factory(3)->sequence(
            ['status' => 'todo', 'user_id' => $user2->id],
            ['status' => 'in_progress', 'user_id' => $user2->id],
            ['status' => 'done', 'user_id' => $user2->id],
        )->create();
    }
}
