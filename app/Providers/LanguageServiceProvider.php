<?php

namespace App\Providers;

use App\Services\LanguageService;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class LanguageServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register bindings in the container.
     */
    public function boot(): void
    {
    }

    /**
     * Register the service provider.
     */
    public function register(): void
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
