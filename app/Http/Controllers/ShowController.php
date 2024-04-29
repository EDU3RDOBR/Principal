<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ShowController extends Controller
{
    // Método para exibir a página de seleção de tabela
    public function selectTable()
    {
        // Obtém o esquema do banco de dados e as tabelas, excluindo algumas tabelas específicas
        $schema = config('database.connections.'.config('database.default').'.schema', 'public');
        $excludedTables = ['migrations', 'personal_access_tokens'];
        $tables = DB::table('information_schema.tables')
                    ->where('table_schema', $schema)
                    ->where('table_type', 'BASE TABLE')
                    ->whereNotIn('table_name', $excludedTables)
                    ->get(['table_name']);
        
        // Retorna a view 'select_table' com as tabelas recuperadas
        return view('select_table', ['tables' => $tables]);
    }

    // Método para exibir os dados de uma tabela específica
    public function showTable(Request $request, $table)
    {
        // Verifica se a tabela existe no banco de dados
        if (!Schema::hasTable($table)) {
            return redirect()->back()->with('error', 'A tabela não existe.');
        }
    
        // Define as opções de itens por página
        $perPageOptions = [10, 20, 50, 100, 500];
        $perPage = $request->input('perPage', session('perPage', $perPageOptions[0]));
    
        try {
            // Obtém os dados da tabela e os ordena por ID
            $query = DB::table($table)->orderBy('id', 'asc');
            $data = $query->paginate($perPage);
        } catch (\Exception $e) {
            // Em caso de erro, redireciona de volta com uma mensagem de erro
            return redirect()->back()->with('error', "Erro ao acessar a tabela {$table} diretamente do banco de dados: {$e->getMessage()}");
        }
    
        // Define a quantidade de itens por página e retorna a view 'show_table' com os dados
        $request->session()->put('perPage', $perPage);
        return view('show_table', ['data' => $data, 'tableName' => $table, 'perPage' => $perPage, 'perPageOptions' => $perPageOptions]);
    }

    // Método para exibir o formulário de edição de um registro
    public function editView($table, $id)
    {
        // Verifica se a tabela existe no banco de dados
        if (!Schema::hasTable($table)) {
            abort(404, "Tabela não encontrada.");
        }

        // Obtém os dados do registro a ser editado
        try {
            $data = DB::table($table)->where('id', $id)->first();
            if (!$data) {
                abort(404, "Registro não encontrado na tabela.");
            }
        } catch (\Exception $e) {
            // Em caso de erro, aborta com uma mensagem de erro
            abort(404, "Erro ao acessar a tabela: " . $e->getMessage());
        }

        // Retorna a view 'update_data' com os dados do registro
        return view('update_data', ['table' => $table, 'row' => $data]);
    }

    // Método para editar os dados de um registro
    public function editData(Request $request, $table, $id)
    {
        // Verifica se a tabela existe no banco de dados
        if (!Schema::hasTable($table)) {
            return redirect()->back()->with('error', 'Tabela não encontrada.');
        }

        // Obtém os dados do registro a ser editado
        $data = DB::table($table)->where('id', $id)->first();
        if (!$data) {
            return redirect()->back()->with('error', "Registro não encontrado.");
        }

        // Obtém as colunas da tabela e define as regras de validação
        $columns = Schema::getColumnListing($table);
        $rules = array_fill_keys(array_diff($columns, ['id', 'created_at', 'updated_at']), 'required');

        // Verifica se a requisição é do tipo PUT e valida os dados
        if ($request->isMethod('put')) {
            $validatedData = $request->validate($rules);
            try {
                // Atualiza os dados do registro no banco de dados
                DB::table($table)->where('id', $id)->update($validatedData);
                return redirect()->route('show.table', ['table' => $table])->with('success', "Registro atualizado com sucesso.");
            } catch (\Exception $e) {
                // Em caso de erro, retorna com uma mensagem de erro
                return redirect()->back()->with('error', "Erro ao atualizar o registro: {$e->getMessage()}");
            }
        }

        // Retorna a view 'update_data' com os dados do registro
        return view('update_data', ['table' => $table, 'row' => $data]);
    }

    // Método para excluir um registro
    public function deleteData(Request $request, $table, $id)
    {
        // Verifica se a tabela existe no banco de dados
        if (!Schema::hasTable($table)) {
            return redirect()->back()->with('error', 'Tabela não encontrada.');
        }

        // Exclui o registro da tabela
        try {
            DB::table($table)->where('id', $id)->delete();
            return redirect()->route('show.table',  ['table' => $table])->with('success', 'Registro excluído com sucesso.');
        } catch (\Exception $e) {
            // Em caso de erro, retorna com uma mensagem de erro
            return redirect()->back()->with('error', "Erro ao excluir o registro: {$e->getMessage()}");
        }
    }

    // Método para excluir múltiplos registros
    public function deleteMultiple(Request $request, $table)
    {
        // Obter os IDs dos registros a serem deletados
        $ids = $request->input('ids', []);
    
        // Verificar se há IDs para deletar
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Nenhum registro foi selecionado para exclusão.');
        }
    
        // Realizar a exclusão dos registros
        DB::table($table)->whereIn('id', $ids)->delete();
    
        return redirect()->back()->with('success', 'Registros excluídos com sucesso.');
    }
}
