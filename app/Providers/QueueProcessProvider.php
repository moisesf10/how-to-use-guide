<?php

namespace App\Providers;

use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;

class QueueProcessProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        /*
        Queue::after(function (JobProcessed $event) {
            // $event->connectionName
            // $event->job
            // $event->job->payload()
dd('success', $event);
            Log::info(print_r('success', true));
           // Log::info(print_r($event->job, true));
            Log::info(print_r(unserialize($event->job->payload), true));
        });

        Queue::failing(function (JobFailed $event) {
            dd('failed', $event);
            // $event->connectionName
            // $event->job
            // $event->exception
        }); */
    }
}
