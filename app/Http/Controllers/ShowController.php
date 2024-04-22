<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\Rule;

class ShowController extends Controller
{
    public function selectTable()
    {
        $schema = config('database.connections.'.config('database.default').'.schema', 'public');
        $excludedTables = ['migrations', 'personal_access_tokens'];
        $tables = DB::table('information_schema.tables')
                    ->where('table_schema', $schema)
                    ->where('table_type', 'BASE TABLE')
                    ->whereNotIn('table_name', $excludedTables)
                    ->get(['table_name']);
        return view('select_table', ['tables' => $tables]);
    }

    public function showTable(Request $request, $table)
    {
        $model = Str::studly(Str::singular($table));
        $modelClass = 'App\\Models\\' . $model;

        if (!Schema::hasTable($table)) {
            return redirect()->back()->with('error', 'A tabela não existe.');
        }

        $perPage = $request->input('perPage', 10);
        $perPageOptions = [10, 20, 50, 100, 500];

        if (class_exists($modelClass)) {
            try {
                $data = $modelClass::paginate($perPage);
            } catch (\Exception $e) {
                return redirect()->back()->with('error', "Erro ao acessar a tabela através do modelo {$modelClass}: {$e->getMessage()}");
            }
        } else {
            try {
                $data = DB::table($table)->paginate($perPage);
            } catch (\Exception $e) {
                return redirect()->back()->with('error', "Erro ao acessar a tabela {$table} diretamente do banco de dados: {$e->getMessage()}");
            }
        }

        return view('show_table', ['data' => $data, 'modelName' => $model, 'perPage' => $perPage, 'perPageOptions' => $perPageOptions]);
    }

    public function editView($modelName, $id)
    {
        $normalizedModelName = strtolower($modelName);
        $modelFiles = scandir(app_path('Models'));
        $modelClassName = null;

        foreach ($modelFiles as $file) {
            if (strtolower(pathinfo($file, PATHINFO_FILENAME)) === $normalizedModelName) {
                $modelClassName = pathinfo($file, PATHINFO_FILENAME);
                break;
            }
        }

        if (!$modelClassName) {
            abort(404, "Modelo não encontrado.");
        }

        $modelClass = 'App\\Models\\' . $modelClassName;
        $data = $modelClass::findOrFail($id);
        return view('update_data', ['model' => $modelClassName, 'row' => $data]);
    }

    public function editData(Request $request, $modelName, $id)
    {
        $normalizedModelName = strtolower($modelName);
        $modelFiles = scandir(app_path('Models'));
        $modelClassName = null;
    
        foreach ($modelFiles as $file) {
            if (strtolower(pathinfo($file, PATHINFO_FILENAME)) === $normalizedModelName) {
                $modelClassName = pathinfo($file, PATHINFO_FILENAME);
                break;
            }
        }
    
        if (!$modelClassName) {
            abort(404, "Modelo não encontrado.");
        }
    
        $modelClass = 'App\\Models\\' . $modelClassName;
    
        try {
            $data = $modelClass::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', "Registro não encontrado.");
        }
    
        if ($request->isMethod('post')) {
            $validatedData = $request->validate([
                // Defina as regras de validação para os campos do modelo aqui
            ]);
    
            try {
                $data->fill($validatedData)->save();
            } catch (\Exception $e) {
                return redirect()->back()->with('error', "Erro ao atualizar o registro: {$e->getMessage()}");
            }
    
            return redirect()->back()->with('success', "Registro atualizado com sucesso.");
        }
    
        return view('update_data', ['model' => $modelClassName, 'row' => $data]);
    }
    
}
