<?php

namespace App\Providers;

use App\Services\FormattingService;
use Illuminate\Support\ServiceProvider;

class FormattingServiceProvider extends ServiceProvider
{
    protected $defer = true;

    public function register()
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
