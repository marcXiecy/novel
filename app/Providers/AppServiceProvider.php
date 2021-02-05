<?php

namespace App\Providers;

use App\Services\CommonService;
use App\Services\NovelService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('CommonService', function ($app) {
            return new CommonService();
        });
        $this->app->bind('NovelService', function ($app) {
            return new NovelService();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
