<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::table('todos')->whereNull('user_id')->exists()) {
            $defaultUserId = DB::table('users')->orderBy('id')->value('id');

            if (! $defaultUserId) {
                $defaultUserId = DB::table('users')->insertGetId([
                    'name' => 'System',
                    'email' => 'system@localhost',
                    'password' => bcrypt(Str::random(32)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('todos')
                ->whereNull('user_id')
                ->update(['user_id' => $defaultUserId]);
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