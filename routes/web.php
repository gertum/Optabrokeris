<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\JobsController;
use App\Http\Controllers\ProfileController;
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

Route::middleware('auth')->group(
    function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        Route::get('/', [DashboardController::class, 'home'])->name('home');
        // Render Jobs component as main page
        Route::get('/jobs', [JobsController::class, 'list'])->name('jobs.list');

        // other routes
        Route::get('/job/{job?}', [JobsController::class, 'view'])->name('jobs.view');
        Route::get('/job-create', [JobsController::class, 'create'])->name('jobs.create');
        Route::get('/subjects', [SubjectController::class, 'list'])->name('subjects.list');

        Route::get('/download/school-example', [DownloadController::class, 'downloadSchoolExample'])->name(
            'download.school.example'
        )
        ;

//    Route::get('/report-results', [ResultsReportController::class, 'getResults'])->name('report.results');

        Route::get('/downloads', [DownloadController::class, 'view'])->name('downloads.view');
        Route::get(
            '/download/roster-subjects-example',
            [DownloadController::class, 'downloadRosterSubjectsExample']
        )->name('download.roster.subjects.example')
        ;
        Route::get(
            '/download/roster-schedule-example',
            [DownloadController::class, 'downloadRosterScheduleExample']
        )->name('download.roster.schedule.example')
        ;
        Route::get(
            '/download/roster-preferred-timings1-example',
            [DownloadController::class, 'downloadPreferredTimings1Example']
        )->name('download.roster.preferred1.example')
        ;
        Route::get(
            '/download/roster-preferred-timings2-example',
            [DownloadController::class, 'downloadPreferredTimings2Example']
        )->name('download.roster.preferred2.example')
        ;
    }
)
;

// Auth routes
require __DIR__ . '/auth.php';
