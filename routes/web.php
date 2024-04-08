<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ImportController;

use App\Http\Controllers\CsvImportController;
use App\Http\Controllers\ShowController;     

use App\Http\Controllers\HomeController;


Route::get('/', [ShowController::class, 'selectTable'])->name('select.table');
Route::get('/show-table/{table}', [ShowController::class, 'showTable'])->name('show.table');
Route::put('/update-data/{id}', [ShowController::class, 'editData'])->name('update-data');
Route::get('/edit-data/{modelName}/{id}', [ShowController::class, 'editData'])->name('edit-data');
Route::get('/delete-data/{id}', [ShowController::class, 'deleteData'])->name('delete-data');
Route::post('/import-csv', [ShowController::class, 'importCsv'])->name('import-csv');

Route::get('/upload-csv', function () {
    return view('upload_csv');
})->name('upload.csv');

Route::post('/upload-csv', [CsvImportController::class, 'upload'])->name('upload.csv.process');
Route::post('/import-csv', [CsvImportController::class, 'import'])->name('import');
Route::get('/cancel-import', [CsvImportController::class, 'cancelImport'])->name('cancel-import');

