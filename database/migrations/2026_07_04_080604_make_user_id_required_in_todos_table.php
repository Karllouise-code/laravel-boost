<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $defaultUserId = DB::table('users')->orderBy('id')->value('id');

        if (! $defaultUserId) {
            throw new RuntimeException('Cannot make todos.user_id NOT NULL because no users exist.');
        }

        DB::table('todos')
            ->whereNull('user_id')
            ->update(['user_id' => $defaultUserId]);

        if (DB::table('todos')->whereNull('user_id')->exists()) {
            throw new RuntimeException('Some todos still have NULL user_id.');
        }

        Schema::table('todos', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('todos', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();
        });
    }
};