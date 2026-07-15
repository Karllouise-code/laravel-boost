<?php

use App\Models\Board;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Broadcast::channel('board.{slug}', function ($user, string $slug) {
        $board = Board::where('slug', $slug)->first();

        if (! $board) {
            return false;
        }

        if ($user->can('view', $board)) {
            return ['id' => $user->id, 'name' => $user->name];
        }

        return false;
    });

    Broadcast::channel('presence-board.{slug}', function ($user, string $slug) {
        $board = Board::where('slug', $slug)->first();

        if (! $board) {
            return false;
        }

        if ($user->can('view', $board)) {
            return ['id' => $user->id, 'name' => $user->name];
        }

        return false;
    });
});
