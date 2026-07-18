<?php

use App\Http\Controllers\BoardController;
use App\Http\Controllers\ColumnController;
use App\Http\Controllers\DatabaseController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SharedBoardController;
use App\Http\Controllers\TodoController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::redirect('/dashboard', '/boards')->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Boards
    Route::get('/boards', [BoardController::class, 'index'])->name('boards.index');
    Route::post('/boards', [BoardController::class, 'store'])->name('boards.store');
    Route::get('/boards/{slug}', [BoardController::class, 'show'])
        ->middleware('board.access')->name('boards.show');
    Route::patch('/boards/{slug}', [BoardController::class, 'update'])
        ->middleware('board.access')->name('boards.update');
    Route::delete('/boards/{slug}', [BoardController::class, 'destroy'])
        ->middleware('board.access')->name('boards.destroy');

    // Collaborators
    Route::post('/boards/{slug}/collaborators', [BoardController::class, 'addCollaborator'])
        ->middleware('board.access')->name('boards.collaborators.store');
    Route::delete('/boards/{slug}/collaborators/{userId}', [BoardController::class, 'removeCollaborator'])
        ->middleware('board.access')->name('boards.collaborators.destroy');

    // Shared board access
    Route::get('/shared/{slug}', [SharedBoardController::class, 'show'])->name('shared.show');
    Route::post('/shared/{slug}/join', [SharedBoardController::class, 'join'])->name('shared.join');

    // Todos (scoped under board)
    Route::get('/boards/{slug}/todos/create', [TodoController::class, 'create'])
        ->middleware('board.access')->name('todos.create');
    Route::post('/boards/{slug}/todos', [TodoController::class, 'store'])
        ->middleware('board.access')->name('todos.store');
    Route::patch('/boards/{slug}/todos/reorder', [TodoController::class, 'reorder'])
        ->middleware('board.access')->name('todos.reorder');
    Route::get('/boards/{slug}/todos/{todo}', [TodoController::class, 'show'])
        ->middleware('board.access')->name('todos.show');
    Route::get('/boards/{slug}/todos/{todo}/edit', [TodoController::class, 'edit'])
        ->middleware('board.access')->name('todos.edit');
    Route::patch('/boards/{slug}/todos/{todo}', [TodoController::class, 'update'])
        ->middleware('board.access')->name('todos.update');
    Route::delete('/boards/{slug}/todos/{todo}', [TodoController::class, 'destroy'])
        ->middleware('board.access')->name('todos.destroy');

    // Columns (scoped under board)
    Route::patch('/boards/{slug}/columns/reorder', [ColumnController::class, 'reorder'])
        ->middleware('board.access')->name('columns.reorder');
    Route::post('/boards/{slug}/columns', [ColumnController::class, 'store'])
        ->middleware('board.access')->name('columns.store');
    Route::patch('/boards/{slug}/columns/{column}', [ColumnController::class, 'update'])
        ->middleware('board.access')->name('columns.update');
    Route::delete('/boards/{slug}/columns/{column}', [ColumnController::class, 'destroy'])
        ->middleware('board.access')->name('columns.destroy');

    // CSV export (scoped under board)
    Route::get('/boards/{slug}/todos/export', [DatabaseController::class, 'export'])
        ->middleware('board.access')->name('todos.export');
});

require __DIR__.'/auth.php';
