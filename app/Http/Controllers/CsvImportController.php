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
    // Função para lidar com o upload do arquivo CSV
    public function upload(Request $request)
    {
        // Validação do arquivo CSV enviado
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);
    
        // Obter o arquivo CSV enviado
        $csvFile = $request->file('csv_file');
        // Obter o nome do modelo a partir do nome do arquivo
        $modelName = pathinfo($csvFile->getClientOriginalName(), PATHINFO_FILENAME);
        // Criar um nome de tabela a partir do nome do modelo
        $tableName = Str::snake($modelName);
    
        // Verificar se a tabela já existe no banco de dados
        if (Schema::hasTable($tableName)) {
            // Obter os dados do CSV
            $csvData = $this->getCsvData($csvFile);
            // Armazenar os dados do CSV na sessão
            session(['csv_model_name' => $modelName, 'csv_table_name' => $tableName, 'csv_data' => $csvData]);
            // Retornar a view para opções de tabela existente
            return view('csv_existing_table_options', ['tableName' => $tableName]);
        } else {
            // Sinalizar que a tabela foi criada nesta sessão
            session(['new_table_created' => true]);
        }

        // Criar um novo modelo baseado no nome do modelo
        Artisan::call('make:model', ['name' => $modelName]);

        // Obter o caminho do modelo recém-criado
        $modelPath = app_path("Models/{$modelName}.php");
        // Obter o conteúdo do modelo
        $modelContent = file_get_contents($modelPath);
        // Adicionar propriedades ao modelo para definir a tabela e os campos guardados
        $guardedProperty = "protected \$guarded = [];\n";
        $tableProperty = "protected \$table = '{$tableName}';\n";
        $modelContent = str_replace("class {$modelName} extends Model\n{", "class {$modelName} extends Model\n{\n    {$guardedProperty}\n    {$tableProperty}", $modelContent);
        // Escrever o conteúdo modificado de volta ao arquivo do modelo
        file_put_contents($modelPath, $modelContent);

        // Criar uma migração a partir dos dados do CSV
        $this->createMigrationFromCsv($csvFile, $tableName);
        // Executar as migrações pendentes
        Artisan::call('migrate');

        // Obter os dados do CSV novamente
        $csvData = $this->getCsvData($csvFile);

        // Armazenar os dados do CSV na sessão
        session(['csv_model_name' => $modelName, 'csv_table_name' => $tableName, 'csv_data' => $csvData]);

        // Retornar a view para visualização prévia dos dados do CSV
        return view('csv_preview', ['data' => $csvData, 'modelName' => $modelName]);
    }

    // Função para importar os dados do CSV para o banco de dados
    public function import()
    {
        try {
            DB::beginTransaction();
            // Obter o nome do modelo, nome da tabela e dados do CSV da sessão
            $modelName = session('csv_model_name');
            $tableName = session('csv_table_name');
            $csvData = session('csv_data');
    
            // Construir o caminho completo para o modelo
            $modelClass = 'App\\Models\\' . $modelName;
            // Criar registros no banco de dados para cada linha do CSV
            foreach ($csvData as $record) {
                $modelClass::create($record);
            }
    
            // Limpar os dados da sessão após a importação
            session()->forget(['csv_model_name', 'csv_table_name', 'csv_data']);
            DB::commit();
    
            // Redirecionar de volta à página inicial com uma mensagem de sucesso
            return redirect('/')->with('success', 'Dados importados com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }

    // Função para cancelar a importação e remover os dados e tabelas relacionados
    public function cancelImport()
    {
        // Obter o nome do modelo e nome da tabela da sessão
        $modelName = session('csv_model_name');
        $tableName = session('csv_table_name');

        // Verificar se a tabela existe e, se existir, removê-la
        if (Schema::hasTable($tableName)) {
            Schema::drop($tableName);
        }
        
        // Excluir o arquivo de migração relacionado à tabela
        $migrationName = 'create_' . $tableName . '_table';
        $migrationFile = glob(database_path('migrations/*_' . $migrationName . '.php'));
        if (!empty($migrationFile)) {
            $migrationFilePath = array_shift($migrationFile);
            unlink($migrationFilePath);
        }
        // Se o modelo existir, excluí-lo também
        if (class_exists($modelClass = 'App\\Models\\' . $modelName)) {
            unlink(app_path("Models/{$modelName}.php"));
        }

        // Limpar os dados da sessão
        session()->forget(['csv_model_name', 'csv_table_name', 'csv_data']);

        // Redirecionar de volta à página de upload com uma mensagem de sucesso
        return redirect('/upload-csv')->with('success', 'Importação cancelada e dados removidos com sucesso.');
    }

    // Função para cancelar todas as importações e limpar os dados da sessão
    public function cancelImports()
    {
        // Obter o nome do modelo e nome da tabela da sessão e limpar os dados da sessão
        $modelName = session('csv_model_name');
        $tableName = session('csv_table_name');
        session()->forget(['csv_model_name', 'csv_table_name', 'csv_data']);
        // Redirecionar de volta à página inicial com uma mensagem de sucesso
        return redirect('/')->with('success', 'Importação cancelada, nenhuma alteração foi feita.');
    }
    

    // Função privada para criar a migração com base no CSV
    private function createMigrationFromCsv($csvFile, $tableName)
    {
        // Criar um objeto de leitura CSV
        $csv = Reader::createFromPath($csvFile->getRealPath(), 'r');
        // Definir o deslocamento do cabeçalho (a primeira linha contém os nomes das colunas)
        $csv->setHeaderOffset(0);
        // Obter os cabeçalhos (nomes das colunas)
        $headers = $csv->getHeader();
    
        // Construir a definição de coluna para a migração com base nos cabeçalhos do CSV
        $columns = '';
        foreach ($headers as $header) {
            $columns .= "\$table->string('" . Str::snake($header) . "')->nullable();\n            ";
        }
        // Obter o conteúdo do arquivo de modelo de migração stub
        $migrationStub = file_get_contents(database_path('stubs/migration.stub'));
        // Substituir as placeholders no stub pelo nome da tabela e definição de coluna
        $migrationStub = str_replace('{{tableName}}', $tableName, $migrationStub);
        $migrationStub = str_replace('{{columns}}', $columns, $migrationStub);
    
        // Gerar o nome e o caminho do arquivo de migração
        $migrationName = 'create_' . $tableName . '_table';
        $datePrefix = date('Y_m_d_His');
        $migrationPath = database_path('migrations/' . $datePrefix . '_' . $migrationName . '.php');
    
        // Escrever o conteúdo do stub modificado no arquivo de migração
        file_put_contents($migrationPath, $migrationStub);
    }
    

    // Função privada para obter os dados do CSV
    private function getCsvData($csvFile)
    {
        // Criar um objeto de leitura CSV
        $csv = Reader::createFromPath($csvFile->getRealPath(), 'r');
        // Definir o deslocamento do cabeçalho (a primeira linha contém os nomes das colunas)
        $csv->setHeaderOffset(0);
        // Obter os cabeçalhos (nomes das colunas)
        $headers = $csv->getHeader();

        // Inicializar um array para armazenar os dados do CSV
        $data = [];
        // Iterar sobre cada linha do CSV e converter os dados para um formato associativo
        foreach ($csv as $record) {
            $row = [];
            foreach ($headers as $header) {
                $row[Str::snake($header)] = $record[$header];
            }
            $data[] = $row;
        }
        // Retornar os dados do CSV convertidos
        return $data;
    }
}
