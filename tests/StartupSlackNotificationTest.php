<?php

namespace Krisell\StartupSlackNotificationLaravel\Tests;

use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\SlackChannelServiceProvider;
use Krisell\StartupSlackNotificationLaravel\Notifications\ServerStartupNotification;

class StartupSlackNotificationTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            'Krisell\StartupSlackNotificationLaravel\StartupSlackNotificationServiceProvider',
            SlackChannelServiceProvider::class,
        ];
    }

    /** @test */
    function the_command_is_registered_and_outputs_expected_info()
    {
        Notification::fake();

        $this->artisan('startup-notification:slack')
            ->expectsOutput('Startup slack notification was sent!');
    }

    /** @test */
    function the_env_slack_hook_is_set()
    {
        Notification::fake();
        config(['startup-slack-notification.slack-hook' => 'my-test-slack-hook']);

        $this->artisan('startup-notification:slack');

        Notification::assertSentTo(
            new AnonymousNotifiable, ServerStartupNotification::class, function (...$params) {
                return 'my-test-slack-hook' === $params[2]->routes['slack'];
            }
        );
    }

    /** @test */
    function the_real_slack_hook_sends_a_noficiation()
    {
        $this->withoutExceptionHandling();
        config([
            'startup-slack-notification.slack-hook' => file_get_contents(__DIR__.'/.hook'),
            'deployed-version.version' => 'my-test-suite-version',
        ]);

        config(['app.name' => 'Phpunit testsuite of slack-package.']);

        $this->artisan('startup-notification:slack');

        $this->assertTrue(true);
    }
}
