<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShowController;
use App\Http\Controllers\CsvImportController;

// Rota inicial que lista as tabelas
Route::get('/', [ShowController::class, 'selectTable'])->name('select.table');

// Rota para mostrar dados de uma tabela específica
Route::get('/show-table/{table}', [ShowController::class, 'showTable'])->name('show.table');

// Rota GET para visualização dos dados a serem editados
Route::get('/edit-data/{modelName}/{id}', [ShowController::class, 'editView'])->name('edit-data');

// Rota para deletar dados
Route::get('/delete-data/{id}', [ShowController::class, 'deleteData'])->name('delete-data');
// Rota POST para editar dados
Route::post('/edit-data/{modelName}/{id}', [ShowController::class, 'editData'])->name('edit-data.post');

