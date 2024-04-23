<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShowController;
use App\Http\Controllers\CsvImportController;

// Rota inicial que lista as tabelas
Route::get('/', [ShowController::class, 'selectTable'])->name('select.table');

// Rota para mostrar dados de uma tabela específica
Route::get('/show-table/{table}', [ShowController::class, 'showTable'])->name('show.table');

// Rota GET para visualização dos dados a serem editados
Route::get('/edit-data/{table}/{id}', [ShowController::class, 'editView'])->name('edit-data');

// Rota para deletar dados
Route::delete('/delete-data/{table}/{id}', [ShowController::class, 'deleteData'])->name('delete-data');

// Rota POST para editar dados
Route::put('/edit-data/{table}/{id}', [ShowController::class, 'editData'])->name('edit-data.put');

Route::get('/upload-csv', function () {
    return view('upload_csv');
})->name('upload.csv');

Route::post('/upload-csv', [CsvImportController::class, 'upload'])->name('upload.csv.process');
Route::post('/import-csv', [CsvImportController::class, 'import'])->name('import');
Route::get('/cancel-import', [CsvImportController::class, 'cancelImport'])->name('cancel-import');