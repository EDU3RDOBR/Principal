<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB; // Importe o facade DB

class ShowController extends Controller
{
    public function selectTable()
    {
        // Assume-se que o esquema padrão seja 'public'. Ajuste conforme necessário.
        $schema = config('database.connections.'.config('database.default').'.schema', 'public');
    
        $excludedTables = ['migrations', 'personal_access_tokens'];
    
        $tables = \Illuminate\Support\Facades\DB::table('information_schema.tables')
                    ->where('table_schema', $schema)
                    ->where('table_type', 'BASE TABLE')
                    ->whereNotIn('table_name', $excludedTables)
                    ->get(['table_name']);
    
        return view('select_table', ['tables' => $tables]);
    }
    

    public function showTable($table)
    {
        $model = \Illuminate\Support\Str::studly(\Illuminate\Support\Str::singular($table));
        $modelClass = 'App\\Models\\' . $model;
    
        // Verifica se a tabela existe usando o Laravel Schema Builder
        if (!\Illuminate\Support\Facades\Schema::hasTable($table)) {
            return redirect()->back()->with('error', 'A tabela não existe.');
        }
    
        if (class_exists($modelClass)) {
            // Tenta recuperar os dados da tabela/modelo com paginação.
            try {
                $data = $modelClass::paginate(10); // 10 itens por página, você pode ajustar conforme necessário
            } catch (\Exception $e) {
                // Log do erro para depuração
                \Log::error("Erro ao acessar a tabela através do modelo {$modelClass}: {$e->getMessage()}");
                return redirect()->back()->with('error', 'Erro ao acessar os dados da tabela.');
            }
        } else {
            // Se o modelo não existir, tenta recuperar os dados diretamente do banco de dados com paginação.
            try {
                $data = \Illuminate\Support\Facades\DB::table($table)->paginate(10); // 10 itens por página, você pode ajustar conforme necessário
            } catch (\Exception $e) {
                \Log::error("Erro ao acessar a tabela {$table} diretamente do banco de dados: {$e->getMessage()}");
                return redirect()->back()->with('error', 'Erro ao acessar os dados da tabela diretamente do banco de dados.');
            }
        }
    
        return view('show_table', ['data' => $data, 'modelName' => $model]);
    }

    public function index(Request $request, $table)
    {
        // Transforma o nome da tabela em um nome de modelo
        $modelName = Str::studly(Str::singular($table));
        $modelClass = 'App\\Models\\' . $modelName;
    
        // Verifica se o modelo existe
        if (!class_exists($modelClass)) {
            return redirect()->back()->with('error', 'O modelo não existe.');
        }
    
        // Obtém as opções de quantidade por página
        $perPageOptions = [5, 10, 15, 20];
    
        // Obtém a quantidade por página atual, ou usa o padrão (10)
        $perPage = $request->query('perPage', 15);
    
        // Obtém os dados paginados do modelo
        try {
            $data = $modelClass::paginate($perPage);
        } catch (\Exception $e) {
            \Log::error("Erro ao acessar a tabela através do modelo {$modelClass}: {$e->getMessage()}");
            return redirect()->back()->with('error', 'Erro ao acessar os dados da tabela.');
        }
    
        // Retorna a view com os dados e as opções de quantidade por página
        return view('show_table', compact('data', 'perPageOptions', 'perPage', 'modelName'));
    }
    public function editData($modelName, $id)
    {
        // Obter os dados da tabela genérica com base no $id
        $row = DB::table($modelName)->find($id);
    
        // Passar os dados para a view
        return view('edit_data', compact('row'));
    }
    
    
}
