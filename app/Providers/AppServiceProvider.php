<?php

namespace App\Providers;

use App\Domain\Roster\Hospital\ScheduleWriter;
use App\Repositories\SubjectRepository;
use App\Transformers\Roster\AmbulanceOfficeDataHandler;
use App\Transformers\SpreadSheetHandlerFactory;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SpreadSheetHandlerFactory::class, function (Application $app) {
            return new SpreadSheetHandlerFactory(config('features.excel_headers'));
        });

        $this->app->singleton(AmbulanceOfficeDataHandler::class, function (Application $app) {
            return (new AmbulanceOfficeDataHandler($app->get(ScheduleWriter::class), $app->get(SubjectRepository::class)))
                ->setTemplateFile(config('features.hospital_template_file'));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
