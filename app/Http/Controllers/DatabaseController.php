<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;

class DatabaseController extends Controller
{
    public function export()
    {
        $todos = Todo::where('user_id', auth()->id())->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="todos-export.csv"',
        ];

        $callback = function () use ($todos) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['ID', 'Title', 'Description', 'Priority', 'Status', 'Completed', 'Due Date', 'Created At', 'Updated At']);

            foreach ($todos as $todo) {
                fputcsv($handle, [
                    $todo->id,
                    $todo->title,
                    $todo->description,
                    $todo->priority,
                    $todo->status,
                    $todo->completed ? 'Yes' : 'No',
                    $todo->due_date?->format('Y-m-d'),
                    $todo->created_at?->format('Y-m-d H:i:s'),
                    $todo->updated_at?->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
