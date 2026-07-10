<?php

namespace App\Http\Controllers;

use App\Models\Board;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SharedBoardController extends Controller
{
    public function show(string $slug): Response|RedirectResponse
    {
        $board = Board::where('slug', $slug)->firstOrFail();
        $user = auth()->user();

        if ($board->collaborators()->where('user_id', $user->id)->exists()) {
            return redirect()->route('boards.show', $board->slug);
        }

        return Inertia::render('Shared/Access', [
            'board' => $board->load('owner'),
        ]);
    }

    public function join(string $slug): RedirectResponse
    {
        $board = Board::where('slug', $slug)->firstOrFail();
        $user = auth()->user();

        if (! $board->collaborators()->where('user_id', $user->id)->exists()) {
            $board->collaborators()->attach($user->id);
        }

        return redirect()
            ->route('boards.show', $board->slug)
            ->with('message', 'You joined the board!');
    }
}
