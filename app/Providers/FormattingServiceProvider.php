<?php

namespace App\Providers;

use App\Services\FormattingService;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class FormattingServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function register(): void
    {
        $this->app->singleton('Sonar.FormattingService', function ($app) {
            return new FormattingService();
        });
    }

    public function provides()
    {
        return ['Sonar.FormattingService'];
    }
}
