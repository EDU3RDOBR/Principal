<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use League\Csv\Reader;
use Illuminate\Support\Facades\DB;

class CsvImportController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $csvFile = $request->file('csv_file');
        $modelName = pathinfo($csvFile->getClientOriginalName(), PATHINFO_FILENAME);
        $tableName = Str::snake($modelName);

        Artisan::call('make:model', ['name' => $modelName]);

        $modelPath = app_path("Models/{$modelName}.php");
        $modelContent = file_get_contents($modelPath);
        $guardedProperty = "protected \$guarded = [];\n";
        $modelContent = str_replace("class {$modelName} extends Model\n{", "class {$modelName} extends Model\n{\n    {$guardedProperty}", $modelContent);
        file_put_contents($modelPath, $modelContent);

        $this->createMigrationFromCsv($csvFile, $tableName);
        Artisan::call('migrate');

        $csvData = $this->getCsvData($csvFile);

        session(['csv_model_name' => $modelName, 'csv_table_name' => $tableName, 'csv_data' => $csvData]);

        return view('csv_preview', ['data' => $csvData, 'modelName' => $modelName]);
    }

    public function import()
    {
        $modelName = session('csv_model_name');
        $csvData = session('csv_data');

        $modelClass = 'App\\Models\\' . $modelName;
        foreach ($csvData as $record) {
            $modelClass::create($record);
        }

        session()->forget(['csv_model_name', 'csv_table_name', 'csv_data']);

        return redirect('/')->with('success', 'Dados importados com sucesso!');
    }

    public function cancelImport()
    {
        $modelName = session('csv_model_name');
        $tableName = session('csv_table_name');

        if (Schema::hasTable($tableName)) {
            Schema::drop($tableName);
        }

        if (class_exists($modelClass = 'App\\Models\\' . $modelName)) {
            unlink(app_path("Models/{$modelName}.php"));
        }

        session()->forget(['csv_model_name', 'csv_table_name', 'csv_data']);

        return redirect('/upload-csv')->with('success', 'Importação cancelada e dados removidos com sucesso.');
    }

    private function createMigrationFromCsv($csvFile, $tableName)
    {
        $csv = Reader::createFromPath($csvFile->getRealPath(), 'r');
        $csv->setHeaderOffset(0);
        $headers = $csv->getHeader();

        $columns = '';
        foreach ($headers as $header) {
            $columns .= "\$table->string('" . Str::snake($header) . "')->nullable();\n            ";
        }

        // Add an "s" at the end of the table name if it doesn't already have one
        if (substr($tableName, -1) !== 's') {
            $tableName .= 's';
        }

        $migrationStub = file_get_contents(database_path('stubs/migration.stub'));
        $migrationStub = str_replace('{{tableName}}', $tableName, $migrationStub);
        $migrationStub = str_replace('{{columns}}', $columns, $migrationStub);

        $migrationName = 'create_' . $tableName . '_table';
        $datePrefix = date('Y_m_d_His');
        $migrationPath = database_path('migrations/' . $datePrefix . '_' . $migrationName . '.php');

        file_put_contents($migrationPath, $migrationStub);
    }

    private function getCsvData($csvFile)
    {
        $csv = Reader::createFromPath($csvFile->getRealPath(), 'r');
        $csv->setHeaderOffset(0);
        $headers = $csv->getHeader();

        $data = [];
        foreach ($csv as $record) {
            $row = [];
            foreach ($headers as $header) {
                $row[Str::snake($header)] = $record[$header];
            }
            $data[] = $row;
        }
        return $data;
    }

    public function selectTable()
    {
        // Assume-se que o esquema padrão seja 'public'. Ajuste conforme necessário.
        $schema = config('database.connections.'.config('database.default').'.schema', 'public');
    
        $tables = \Illuminate\Support\Facades\DB::table('information_schema.tables')
                    ->where('table_schema', $schema)
                    ->where('table_type', 'BASE TABLE')
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
            // Tenta recuperar os dados da tabela/modelo.
            try {
                $data = $modelClass::all();
            } catch (\Exception $e) {
                // Log do erro para depuração
                \Log::error("Erro ao acessar a tabela através do modelo {$modelClass}: {$e->getMessage()}");
                return redirect()->back()->with('error', 'Erro ao acessar os dados da tabela.');
            }
        } else {
            // Se o modelo não existir, tenta recuperar os dados diretamente do banco de dados.
            try {
                $data = \Illuminate\Support\Facades\DB::table($table)->get();
            } catch (\Exception $e) {
                \Log::error("Erro ao acessar a tabela {$table} diretamente do banco de dados: {$e->getMessage()}");
                return redirect()->back()->with('error', 'Erro ao acessar os dados da tabela diretamente do banco de dados.');
            }
        }
    
        return view('show_table', ['data' => $data, 'modelName' => $model]);
    }
    
    
    
}
