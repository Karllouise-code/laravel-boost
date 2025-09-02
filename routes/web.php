<?php

use App\Http\Controllers\TodoController;
use App\Http\Controllers\DatabaseController; // Add this line
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/todos');

Route::resource('todos', TodoController::class);

// New route for downloading SQLite database
Route::get('/download-sqlite', [DatabaseController::class, 'downloadSqlite'])->name('download.sqlite');
