<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('todos', function (Blueprint $table) {
            $table->dropForeign(['column_id']);
            $table->foreignId('column_id')->nullable(false)->change();
            $table->foreign('column_id')->references('id')->on('columns')->cascadeOnDelete();
            $table->dropColumn('status');
        });
    }

    public function down(): void
    {
        Schema::table('todos', function (Blueprint $table) {
            $table->dropForeign(['column_id']);
            $table->foreignId('column_id')->nullable()->change();
            $table->foreign('column_id')->references('id')->on('columns')->nullOnDelete();
            $table->enum('status', ['todo', 'in_progress', 'done'])->default('todo')->after('completed');
        });
    }
};
