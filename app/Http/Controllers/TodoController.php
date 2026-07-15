<?php

namespace App\Http\Controllers;

use App\Events\TodoCreated;
use App\Events\TodoDeleted;
use App\Events\TodoReordered;
use App\Events\TodoUpdated;
use App\Http\Requests\DestroyTodoRequest;
use App\Http\Requests\ReorderTodoRequest;
use App\Http\Requests\StoreTodoRequest;
use App\Http\Requests\UpdateTodoRequest;
use App\Models\Board;
use App\Models\Todo;
use Illuminate\Http\JsonResponse;
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

        $todo = $board->todos()->create($request->validated());

        TodoCreated::dispatch($todo);

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

        TodoUpdated::dispatch($todo);

        return redirect()
            ->route('boards.show', $slug)
            ->with('message', 'Todo updated successfully!');
    }

    public function destroy(DestroyTodoRequest $request, string $slug, Todo $todo): RedirectResponse
    {
        $boardSlug = $todo->board->slug;
        $todoId = $todo->id;

        $todo->delete();

        TodoDeleted::dispatch($todoId, $boardSlug);

        return redirect()
            ->route('boards.show', $slug)
            ->with('message', 'Todo deleted successfully!');
    }

    public function reorder(ReorderTodoRequest $request, string $slug): JsonResponse
    {
        $board = Board::where('slug', $slug)->firstOrFail();
        $todo = $board->todos()->findOrFail($request->validated('todo_id'));

        $todo->update([
            'status' => $request->validated('status'),
            'priority' => $request->validated('priority'),
        ]);

        TodoReordered::dispatch($todo);

        return response()->json(['message' => 'Todo reordered']);
    }
}
