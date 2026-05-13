<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

use App\Http\Controllers\BackupController;

Route::post('/backup', [BackupController::class, 'start']);
Route::get('/backup/{jobId}/progress', [BackupController::class, 'progress']);
Route::get('/backup/{jobId}/download', [BackupController::class, 'download']);
