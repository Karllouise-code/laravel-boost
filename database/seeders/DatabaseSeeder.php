<?php

namespace Database\Seeders;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::firstOrCreate([
            'email' => 'test@example.com',
        ], [
            'name' => 'Test User',
        ]);

        Todo::factory(12)->sequence(
            ['status' => 'todo'],
            ['status' => 'todo'],
            ['status' => 'todo'],
            ['status' => 'todo'],
            ['status' => 'in_progress'],
            ['status' => 'in_progress'],
            ['status' => 'in_progress'],
            ['status' => 'in_progress'],
            ['status' => 'done'],
            ['status' => 'done'],
            ['status' => 'done'],
            ['status' => 'done'],
        )->create();
    }
}
