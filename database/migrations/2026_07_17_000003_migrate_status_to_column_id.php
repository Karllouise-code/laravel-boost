<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $boards = DB::table('boards')->get();

        foreach ($boards as $board) {
            $todoCol = DB::table('columns')->insertGetId([
                'board_id' => $board->id,
                'name' => 'To Do',
                'color' => '#6366f1',
                'position' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $inProgressCol = DB::table('columns')->insertGetId([
                'board_id' => $board->id,
                'name' => 'In Progress',
                'color' => '#f59e0b',
                'position' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $doneCol = DB::table('columns')->insertGetId([
                'board_id' => $board->id,
                'name' => 'Done',
                'color' => '#10b981',
                'position' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('todos')
                ->where('board_id', $board->id)
                ->where('status', 'todo')
                ->update(['column_id' => $todoCol]);

            DB::table('todos')
                ->where('board_id', $board->id)
                ->where('status', 'in_progress')
                ->update(['column_id' => $inProgressCol]);

            DB::table('todos')
                ->where('board_id', $board->id)
                ->where('status', 'done')
                ->update(['column_id' => $doneCol]);
        }
    }

    public function down(): void
    {
        // Not reversible without status data
    }
};
