<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('boards', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('board_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('board_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['board_id', 'user_id']);
        });

        Schema::table('todos', function (Blueprint $table) {
            $table->foreignId('board_id')->nullable()->constrained()->cascadeOnDelete()->after('id');
        });

        $users = DB::table('users')->get();
        foreach ($users as $user) {
            $slug = Str::random(8);
            DB::table('boards')->insert([
                'name' => 'My Todos',
                'slug' => $slug,
                'owner_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $boardId = DB::getPdo()->lastInsertId();

            DB::table('board_user')->insert([
                'board_id' => $boardId,
                'user_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('todos')
                ->where('user_id', $user->id)
                ->update(['board_id' => $boardId]);
        }

        Schema::table('todos', function (Blueprint $table) {
            $table->foreignId('board_id')->nullable(false)->change();
        });

        Schema::table('todos', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('todos', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete()->after('id');
        });

        $boards = DB::table('boards')->get();
        foreach ($boards as $board) {
            DB::table('todos')
                ->where('board_id', $board->id)
                ->update(['user_id' => $board->owner_id]);
        }

        Schema::table('todos', function (Blueprint $table) {
            $table->dropForeign(['board_id']);
            $table->dropColumn('board_id');
        });

        Schema::dropIfExists('board_user');
        Schema::dropIfExists('boards');
    }
};
