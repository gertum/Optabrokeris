<?php

namespace App\Providers;

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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
