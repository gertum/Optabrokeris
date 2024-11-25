<?php

use App\Http\Controllers\DownloadController;
use App\Http\Controllers\JobsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResultsReportController;
use App\Http\Controllers\SubjectController;
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

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Render Jobs component as main page
    Route::get('/', [JobsController::class, 'list'])->name('jobs.list');

    // other routes
    Route::get('/jobs/{job?}', [JobsController::class, 'form'])->name('jobs.form');
    Route::get('/subjects', [SubjectController::class, 'list'])->name('subjects.list');

    Route::get('/download/school-example', [DownloadController::class, 'downloadSchoolExample'])->name('download.school.example');
    Route::get('/report-results', [ResultsReportController::class, 'getResults'])->name('report.results');

});

// Auth routes
require __DIR__.'/auth.php';
