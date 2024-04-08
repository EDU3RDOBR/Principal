<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;

class HomeController extends Controller
{
    public function index($tableName = null)
    {
        $models = collect(File::files(app_path('Models')))
            ->map(function ($file) {
                return pathinfo($file->getFilename(), PATHINFO_FILENAME);
            })
            ->reject(function ($modelName) {
                return !Schema::hasTable($modelName);
            });

        $data = [];
        if ($tableName) {
            $className = 'App\\Models\\' . $tableName;
            $data[$tableName] = $className::all();
        }

        return view('home', ['data' => $data, 'tables' => $models]);
    }
}
