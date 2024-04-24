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
    
        // Verificar se a tabela já existe
        if (Schema::hasTable($tableName)) {
            $csvData = $this->getCsvData($csvFile);
            session(['csv_model_name' => $modelName, 'csv_table_name' => $tableName, 'csv_data' => $csvData]);
            return view('csv_existing_table_options', ['tableName' => $tableName]);
        } else {
            // Sinalizar que a tabela foi criada nesta sessão
            session(['new_table_created' => true]);
        }

        Artisan::call('make:model', ['name' => $modelName]);

        $modelPath = app_path("Models/{$modelName}.php");
        $modelContent = file_get_contents($modelPath);
        $guardedProperty = "protected \$guarded = [];\n";
        $tableProperty = "protected \$table = '{$tableName}';\n";
        $modelContent = str_replace("class {$modelName} extends Model\n{", "class {$modelName} extends Model\n{\n    {$guardedProperty}\n    {$tableProperty}", $modelContent);
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
        $tableName = session('csv_table_name');
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
        
        $migrationName = 'create_' . $tableName . '_table';
        $migrationFile = glob(database_path('migrations/*_' . $migrationName . '.php'));
        if (!empty($migrationFile)) {
            $migrationFilePath = array_shift($migrationFile);
            unlink($migrationFilePath);
        }
        if (class_exists($modelClass = 'App\\Models\\' . $modelName)) {
            unlink(app_path("Models/{$modelName}.php"));
        }

        session()->forget(['csv_model_name', 'csv_table_name', 'csv_data']);

        return redirect('/upload-csv')->with('success', 'Importação cancelada e dados removidos com sucesso.');
    }

    public function cancelImports()
    {
        $modelName = session('csv_model_name');
        $tableName = session('csv_table_name');
        session()->forget(['csv_model_name', 'csv_table_name', 'csv_data']);
        return redirect('/')->with('success', 'Importação cancelada, nenhuma alteração foi feita.');
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
}
