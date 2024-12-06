<?php

namespace App\Providers;

use App\Domain\Roster\Hospital\DataFileDetector;
use App\Domain\Roster\Hospital\ScheduleParser;
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
        $this->app->singleton(
            SpreadSheetHandlerFactory::class,
            function (Application $app) {
                return new SpreadSheetHandlerFactory(config('features.excel_headers'));
            }
        );

        $this->app->singleton(
            AmbulanceOfficeDataHandler::class,
            function (Application $app) {
                return (new AmbulanceOfficeDataHandler(
                    $app->get(ScheduleWriter::class),
                    $app->get(SubjectRepository::class),
                    $app->get(ScheduleParser::class),
                    $app->get(DataFileDetector::class)
                ))
                    ->setTemplateFile(config('features.hospital_template_file'));
            }
        );
        $this->app->singleton(
            ScheduleParser::class,
            function (Application $app) {
                return (new ScheduleParser())->setParserVersion(2);
            }
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
