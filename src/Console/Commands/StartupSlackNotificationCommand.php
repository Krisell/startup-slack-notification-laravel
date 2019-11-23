<?php

namespace Krisell\StartupSlackNotificationLaravel\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;
use Krisell\StartupSlackNotificationLaravel\Notifications\ServerStartupNotification;

class StartupSlackNotificationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'startup-notification:slack';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends a server statup notification to slack';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Notification::route(
            'slack',
            config('startup-slack-notification.slack-hook')
        )->notify(new ServerStartupNotification());

        $this->info("Startup slack notification was sent!");
    }
}
