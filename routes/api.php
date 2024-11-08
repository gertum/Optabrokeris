<?php

use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\SubjectController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth')->group(function () {
    Route::get('/job', [JobController::class, 'list']);
    Route::get('/job/{job}', [JobController::class, 'view']);
    Route::post('/job', [JobController::class, 'create']);
    Route::put('/job/{job}', [JobController::class, 'update']);
    Route::delete('/job/{job}', [JobController::class, 'delete']);
    Route::post('/job/{job}/solve', [JobController::class, 'solve']);
    Route::post('/job/{job}/stop', [JobController::class, 'stop']);
    Route::post('/job/{job}/upload', [JobController::class, 'upload']);
    Route::get('/job/{job}/download', [JobController::class, 'download']);


    Route::get('/subject', [SubjectController::class, 'list']);
    Route::get('/subject/{subject}', [SubjectController::class, 'view']);
    Route::post('/subject', [SubjectController::class, 'create']);
    Route::put('/subject/{subject}', [SubjectController::class, 'update']);
    Route::delete('/subject/{subject}', [SubjectController::class, 'delete']);

    Route::post('/subject/upsert-json', [SubjectController::class, 'upsertJson']);
    Route::post('/subject/upsert-xlsx', [SubjectController::class, 'upsertXslx']);
});
