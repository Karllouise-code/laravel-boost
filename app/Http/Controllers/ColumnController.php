<?php

namespace App\Http\Controllers;

use App\Events\ColumnCreated;
use App\Events\ColumnDeleted;
use App\Events\ColumnsReordered;
use App\Events\ColumnUpdated;
use App\Http\Requests\StoreColumnRequest;
use App\Http\Requests\UpdateColumnRequest;
use App\Models\Board;
use App\Models\Column;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ColumnController extends Controller
{
    public function store(StoreColumnRequest $request, string $slug): RedirectResponse
    {
        $board = Board::where('slug', $slug)->firstOrFail();
        $maxPosition = $board->columns()->max('position') ?? -1;

        $column = Column::create([
            'board_id' => $board->id,
            'name' => $request->validated('name'),
            'color' => $request->validated('color'),
            'position' => $maxPosition + 1,
        ]);

        broadcast(new ColumnCreated($column));

        return back()->with('message', 'Column created successfully!');
    }

    public function update(UpdateColumnRequest $request, string $slug, int $columnId): RedirectResponse
    {
        $column = Column::where('id', $columnId)
            ->where('board_id', Board::where('slug', $slug)->firstOrFail()->id)
            ->firstOrFail();

        $column->update($request->validated());

        broadcast(new ColumnUpdated($column));

        return back()->with('message', 'Column updated successfully!');
    }

    public function destroy(string $slug, int $columnId): RedirectResponse
    {
        $board = Board::where('slug', $slug)->firstOrFail();
        $column = Column::where('id', $columnId)->where('board_id', $board->id)->firstOrFail();

        if ($board->columns()->count() <= 1) {
            return back()->withErrors(['name' => 'Cannot delete the last column.']);
        }

        $fallbackColumn = $board->columns()->where('id', '!=', $columnId)->orderBy('position')->first();

        DB::transaction(function () use ($column, $fallbackColumn) {
            $column->todos()->update(['column_id' => $fallbackColumn->id]);
            broadcast(new ColumnDeleted($column));
            $column->delete();
        });

        return back()->with('message', 'Column deleted. Todos moved to '.$fallbackColumn->name.'.');
    }

    public function reorder(Request $request, string $slug): RedirectResponse
    {
        $board = Board::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'columns' => ['required', 'array'],
            'columns.*.id' => ['required', 'integer', 'exists:columns,id'],
            'columns.*.position' => ['required', 'integer', 'min:0'],
        ]);

        DB::transaction(function () use ($validated) {
            $maxPosition = count($validated['columns']);
            foreach ($validated['columns'] as $item) {
                Column::where('id', $item['id'])->update(['position' => $maxPosition + $item['id']]);
            }
            foreach ($validated['columns'] as $item) {
                Column::where('id', $item['id'])->update(['position' => $item['position']]);
            }
        });

        broadcast(new ColumnsReordered($board, $validated['columns']));

        return back()->with('message', 'Columns reordered!');
    }
}
