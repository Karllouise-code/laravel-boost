<?php

namespace App\Http\Middleware;

use App\Models\Board;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureBoardAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $slug = $request->route('slug');
        $board = Board::where('slug', $slug)->firstOrFail();

        if (! $request->user()->can('view', $board)) {
            abort(403, 'You do not have access to this board.');
        }

        $request->attributes->set('board', $board);

        return $next($request);
    }
}
