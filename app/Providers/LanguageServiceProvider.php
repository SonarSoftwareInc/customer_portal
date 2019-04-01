<?php

namespace App\Providers;

use App\Http\ViewComposers\LanguageComposer;
use App\Services\LanguageService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class LanguageServiceProvider extends ServiceProvider
{
    protected $defer = true;
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(LanguageService::class, function ($app) {
            return new LanguageService();
        });
    }

    public function provides()
    {
        return [LanguageService::class];
    }
}