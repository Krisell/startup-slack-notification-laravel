<?php

namespace Krisell\StartupSlackNotificationLaravel;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Krisell\StartupSlackNotificationLaravel\Http\Controllers\VersionController;
use Krisell\StartupSlackNotificationLaravel\Console\Commands\StartupSlackNotificationCommand;

class StartupSlackNotificationServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([StartupSlackNotificationCommand::class]);
        }

        $this->publishes([
            __DIR__.'/config/startup-slack-notification.php' => config_path('startup-slack-notification.php'),
        ]);
    }


    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/startup-slack-notification.php',
            'startup-slack-notification'
        );
    }
}
