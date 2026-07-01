<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Response;

class DatabaseController extends Controller
{
    public function downloadSqlite()
    {
        $databasePath = '/var/www/html/database/database.sqlite';

        if (! file_exists($databasePath)) {
            abort(404, 'SQLite database file not found.');
        }

        return Response::download($databasePath, 'database.sqlite');
    }
}
