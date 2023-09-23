<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\JobsController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use \App\Http\Controllers\DownloadController;

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
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // there are analogs in 'api'
    // these are for testing or for references in front
    Route::get('/jobs', [JobsController::class, 'list'])->name('jobs.list');
    Route::get('/jobs/new', [JobsController::class, 'newJob'])->name('jobs.new');
    Route::get('/jobs/{id}', [JobsController::class, 'view'])->name('job.view');
    Route::get('/testupload', [JobsController::class, 'testUpload']);
    Route::get('/jobs/edit/{jobId}/{jobType}', [JobsController::class, 'editJob'])->name('jobs.edit');

    Route::get('/download/school-example', [DownloadController::class, 'downloadSchoolExample'])->name('download.school.example');

});


require __DIR__.'/auth.php';
