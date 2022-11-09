<?php

// \Debugbar::disable();

use App\Actions\ImportTimesheetAction;
use App\Models\Timesheet;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/import/{method}', function ($method) {
    $action = new ImportTimesheetAction;
    $size = 500;
    match ($method) {
        'fast-excel' => $action->fastExcel($size),
        'fast-excel-chunk' => $action->fastExcelChunk($size),
        'laravel-excel' => $action->laravelExcel($size),
        'chunk-aeng' => $action->chunkAeng($size),
    };

    return Timesheet::count();
});