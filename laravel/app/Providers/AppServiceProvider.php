<?php

namespace App\Providers;

use App\Services\BinanceClient;
use App\Services\CourseJsonStore;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CourseJsonStore::class, fn () => new CourseJsonStore(
            storage_path('app/courses.json'),
            database_path('data/courses.json'),
        ));

        $this->app->singleton(BinanceClient::class, fn () => new BinanceClient(
            config('services.binance.url'),
        ));
    }

    public function boot(): void
    {
    }
}
