<?php

namespace App\Http\Controllers;

use App\Http\Requests\DestroyTodoRequest;
use App\Http\Requests\StoreTodoRequest;
use App\Http\Requests\UpdateTodoRequest;
use App\Models\Board;
use App\Models\Todo;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class TodoController extends Controller
{
    public function create(string $slug): Response
    {
        $board = Board::where('slug', $slug)->firstOrFail();

        return Inertia::render('Todos/Create', [
            'board' => $board,
        ]);
    }

    public function store(StoreTodoRequest $request, string $slug): RedirectResponse
    {
        $board = Board::where('slug', $slug)->firstOrFail();

        $board->todos()->create($request->validated());

        return redirect()
            ->route('boards.show', $slug)
            ->with('message', 'Todo created successfully!');
    }

    public function show(string $slug, Todo $todo): Response
    {
        $board = Board::where('slug', $slug)->firstOrFail();

        return Inertia::render('Todos/Show', [
            'board' => $board,
            'todo' => $todo,
        ]);
    }

    public function edit(string $slug, Todo $todo): Response
    {
        $board = Board::where('slug', $slug)->firstOrFail();

        return Inertia::render('Todos/Edit', [
            'board' => $board,
            'todo' => $todo,
        ]);
    }

    public function update(UpdateTodoRequest $request, string $slug, Todo $todo): RedirectResponse
    {
        $todo->update($request->validated());

        return redirect()
            ->route('boards.show', $slug)
            ->with('message', 'Todo updated successfully!');
    }

    public function destroy(DestroyTodoRequest $request, string $slug, Todo $todo): RedirectResponse
    {
        $todo->delete();

        return redirect()
            ->route('boards.show', $slug)
            ->with('message', 'Todo deleted successfully!');
    }
}
