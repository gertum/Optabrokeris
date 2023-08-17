<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\JobController;

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


Route::get('/job', [JobController::class, 'list']);
Route::get('/job/{id}', [JobController::class, 'view']);

Route::post('/job', [JobController::class, 'create']);
Route::put('/job/{id}', [JobController::class, 'update']);
Route::post('/job/{id}/solve', [JobController::class, 'solve']);

// TODO job upload route from JobController

Route::post('/job/{id}/upload', [JobController::class, 'upload']);
Route::post('/job/{id}/download', [JobController::class, 'download']);