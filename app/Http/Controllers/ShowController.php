<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
        if (!Schema::hasTable($table)) {
            return redirect()->back()->with('error', 'A tabela não existe.');
        }

        $perPage = $request->input('perPage', 10);
        $perPageOptions = [10, 20, 50, 100, 500];

        
        try {
            $data = DB::table($table)->orderBy('id', 'asc')->paginate($perPage);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', "Erro ao acessar a tabela {$table} diretamente do banco de dados: {$e->getMessage()}");
        }

        return view('show_table', ['data' => $data, 'tableName' => $table, 'perPage' => $perPage, 'perPageOptions' => $perPageOptions]);
    }

    public function editView($table, $id)
    {
        if (!Schema::hasTable($table)) {
            abort(404, "Tabela não encontrada.");
        }

        try {
            $data = DB::table($table)->where('id', $id)->first();
            if (!$data) {
                abort(404, "Registro não encontrado na tabela.");
            }
        } catch (\Exception $e) {
            abort(404, "Erro ao acessar a tabela: " . $e->getMessage());
        }

        return view('update_data', ['table' => $table, 'row' => $data]);
    }

    public function editData(Request $request, $table, $id)
    {
        if (!Schema::hasTable($table)) {
            return redirect()->back()->with('error', 'Tabela não encontrada.');
        }

        $data = DB::table($table)->where('id', $id)->first();
        if (!$data) {
            return redirect()->back()->with('error', "Registro não encontrado.");
        }

        $columns = Schema::getColumnListing($table);
        $rules = array_fill_keys(array_diff($columns, ['id', 'created_at', 'updated_at']), 'required');

        if ($request->isMethod('put')) {
            $validatedData = $request->validate($rules);
            try {
                DB::table($table)->where('id', $id)->update($validatedData);
                return redirect()->route('show.table', ['table' => $table])->with('success', "Registro atualizado com sucesso.");
            } catch (\Exception $e) {
                return redirect()->back()->with('error', "Erro ao atualizar o registro: {$e->getMessage()}");
            }
        }

        return view('update_data', ['table' => $table, 'row' => $data]);
    }

    public function deleteData(Request $request, $table, $id)
    {
        if (!Schema::hasTable($table)) {
            return redirect()->back()->with('error', 'Tabela não encontrada.');
        }

        try {
            DB::table($table)->where('id', $id)->delete();
            return redirect()->route('show.table',  ['table' => $table])->with('success', 'Registro excluído com sucesso.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', "Erro ao excluir o registro: {$e->getMessage()}");
        }
    }
}
