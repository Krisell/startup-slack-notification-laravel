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
    function the_specified_slack_hook_is_used()
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
    function the_expected_default_data_is_included_in_the_message()
    {
        Notification::fake();

        $this->artisan('startup-notification:slack');

        Notification::assertSentTo(
            new AnonymousNotifiable, ServerStartupNotification::class, function ($notification) {
                $message = $notification->toSlack();
                $this->assertEquals('', $message->image);
                $this->assertEquals('Server started! - Laravel', $message->content);
                $this->assertEquals('No version set', $message->attachments[0]->fields['Version']);
                $this->assertEquals('testing', $message->attachments[0]->fields['Env']);

                return true;
            }
        );
    }

    /** @test */
    function the_specified_data_is_included_in_the_message()
    {
        Notification::fake();
        config([
            'startup-slack-notification.image' => 'my-image-url',
            'app.name' => 'Test app',
            'deployed-version.version' => '123',
            'app.env' => 'testing-2',
        ]);

        $this->artisan('startup-notification:slack');

        Notification::assertSentTo(
            new AnonymousNotifiable, ServerStartupNotification::class, function ($notification) {
                $message = $notification->toSlack();
                $this->assertEquals('my-image-url', $message->image);
                $this->assertEquals('Server started! - Test app', $message->content);
                $this->assertEquals('123', $message->attachments[0]->fields['Version']);
                $this->assertEquals('testing-2', $message->attachments[0]->fields['Env']);

                return true;
            }
        );
    }

    /** @test */
    function arbitrary_additional_data_can_be_added()
    {
        Notification::fake();
        config([
            'startup-slack-notification.image' => 'my-image-url',
            'app.name' => 'Test app',
            'deployed-version.version' => '123',
            'app.env' => 'testing-2',
            'services.startup-slack-notification.data' => [
                'some' => 'extra',
                'arbitrary' => 'data',
            ],
        ]);

        $this->artisan('startup-notification:slack');

        Notification::assertSentTo(
            new AnonymousNotifiable, ServerStartupNotification::class, function ($notification) {
                $message = $notification->toSlack();
                $this->assertEquals('my-image-url', $message->image);
                $this->assertEquals('Server started! - Test app', $message->content);
                $this->assertEquals('123', $message->attachments[0]->fields['Version']);
                $this->assertEquals('testing-2', $message->attachments[0]->fields['Env']);
                $this->assertEquals('extra', $message->attachments[0]->fields['some']);
                $this->assertEquals('data', $message->attachments[0]->fields['arbitrary']);

                return true;
            }
        );
    }

    /** @test */
    function the_real_slack_hook_sends_a_noficiation()
    {
        config([
            'startup-slack-notification.slack-hook' => file_get_contents(__DIR__.'/.hook'),
            'deployed-version.version' => 'my-test-suite-version',
            'services.startup-slack-notification.data' => [
                'some' => 'extra',
                'arbitrary' => 'data',
            ],
        ]);

        config(['app.name' => 'Phpunit testsuite of slack-package.']);

        $this->artisan('startup-notification:slack');

        $this->assertTrue(true);
    }
}
