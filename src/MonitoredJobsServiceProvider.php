<?php

namespace Aryeo\MonitoredJobs;

use Aryeo\MonitoredJobs\Http\Middleware\HandleInertiaRequests;
use Aryeo\MonitoredJobs\Listeners\HandleJobExceptionOccurred;
use Aryeo\MonitoredJobs\Listeners\HandleJobFailed;
use Aryeo\MonitoredJobs\Listeners\HandleJobProcessed;
use Aryeo\MonitoredJobs\Listeners\HandleJobProcessing;
use Aryeo\MonitoredJobs\Listeners\HandleJobQueued;
use Aryeo\MonitoredJobs\Listeners\HandleJobRetryRequested;
use Illuminate\Foundation\Support\Providers\EventServiceProvider;
use Illuminate\Queue\Events\JobExceptionOccurred;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Events\JobQueued;
use Illuminate\Queue\Events\JobRetryRequested;
use Illuminate\Support\Facades\Route;

class MonitoredJobsServiceProvider extends EventServiceProvider
{
    protected $listen = [
        JobQueued::class => [HandleJobQueued::class],
        JobProcessing::class => [HandleJobProcessing::class],
        JobProcessed::class => [HandleJobProcessed::class],
        JobExceptionOccurred::class => [HandleJobExceptionOccurred::class],
        JobFailed::class => [HandleJobFailed::class],
        JobRetryRequested::class => [HandleJobRetryRequested::class],
    ];

    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        parent::boot();

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'monitored-jobs');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Frontend is not ready for use yet
        // $this->registerRoutes();
        // $this->publishes([
        //     __DIR__.'/../public' => public_path('vendor/monitored-jobs'),
        // ], ['monitored-jobs-assets', 'laravel-assets']);

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('monitored-jobs.php'),
            ], 'monitored-jobs');
        }
    }

    protected function registerRoutes()
    {
        Route::group([
            'middleware' => ['web', HandleInertiaRequests::class],
        ], function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        });
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        parent::register();

        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'monitored-jobs');
    }
}
