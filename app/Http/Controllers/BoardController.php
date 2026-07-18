<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCollaboratorRequest;
use App\Http\Requests\StoreBoardRequest;
use App\Http\Requests\UpdateBoardRequest;
use App\Models\Board;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class BoardController extends Controller
{
    public function index(): Response
    {
        $user = auth()->user();

        $owned = $user->ownedBoards()->withCount('todos')->get();
        $collaborated = $user->collaboratedBoards()->withCount('todos')->get();

        $boards = $owned->merge($collaborated)->unique('id');

        return Inertia::render('Boards/Index', [
            'boards' => $boards,
        ]);
    }

    public function store(StoreBoardRequest $request): RedirectResponse
    {
        $board = Board::create([
            'name' => $request->validated('name'),
            'slug' => Str::random(8),
            'owner_id' => $request->user()->id,
        ]);

        $board->collaborators()->attach($request->user()->id);

        $board->columns()->create(['name' => 'To Do', 'color' => '#6366f1', 'position' => 0]);
        $board->columns()->create(['name' => 'In Progress', 'color' => '#f59e0b', 'position' => 1]);
        $board->columns()->create(['name' => 'Done', 'color' => '#10b981', 'position' => 2]);

        return redirect()
            ->route('boards.show', $board->slug)
            ->with('message', 'Board created successfully!');
    }

    public function show(string $slug): Response
    {
        $board = Board::where('slug', $slug)->firstOrFail();

        $board->load(['columns' => function ($query) {
            $query->orderBy('position');
        }, 'columns.todos' => function ($query) {
            $query->orderBy('priority', 'desc')
                ->orderBy('due_date', 'asc')
                ->orderBy('created_at', 'desc');
        }]);

        return Inertia::render('Boards/Show', [
            'board' => $board->load('owner', 'collaborators'),
            'todos' => $board->columns->flatMap->todos,
        ]);
    }

    public function update(UpdateBoardRequest $request, string $slug): RedirectResponse
    {
        $board = Board::where('slug', $slug)->firstOrFail();
        $board->update($request->validated());

        return redirect()
            ->route('boards.show', $board->slug)
            ->with('message', 'Board updated successfully!');
    }

    public function destroy(string $slug): RedirectResponse
    {
        $board = Board::where('slug', $slug)->firstOrFail();

        if (auth()->user()->cannot('delete', $board)) {
            abort(403);
        }

        $board->delete();

        return redirect()
            ->route('boards.index')
            ->with('message', 'Board deleted successfully!');
    }

    public function addCollaborator(AddCollaboratorRequest $request, string $slug): RedirectResponse
    {
        $board = Board::where('slug', $slug)->firstOrFail();
        $user = User::where('email', $request->validated('email'))->first();

        if (! $board->collaborators()->where('user_id', $user->id)->exists()) {
            $board->collaborators()->attach($user->id);
        }

        return back()->with('message', "{$user->name} has been added as a collaborator.");
    }

    public function removeCollaborator(string $slug, int $userId): RedirectResponse
    {
        $board = Board::where('slug', $slug)->firstOrFail();

        if (auth()->user()->cannot('manageCollaborators', $board)) {
            abort(403);
        }

        if ($userId === $board->owner_id) {
            return back()->withErrors(['error' => 'Cannot remove the board owner.']);
        }

        $board->collaborators()->detach($userId);

        return back()->with('message', 'Collaborator removed.');
    }
}
