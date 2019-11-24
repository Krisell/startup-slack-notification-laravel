<?php

namespace Krisell\StartupSlackNotificationLaravel\Notifications;

use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\SlackMessage;

class ServerStartupNotification extends Notification
{
    public function via($notifiable)
    {
        return ['slack'];
    }

    public function toSlack()
    {
        $fields = [
            'Version' => config('deployed-version.version', 'No version set'),
            'Env' => config('app.env'),
        ];

        if (Storage::exists('isProduction') && Storage::get('isProduction') === 'production') {
            $fields['ProductionFlag'] = 'Yes';
        }

        return (new SlackMessage)
            ->image(config('startup-slack-notification.image'))
            ->success()
            ->content("Server started! – ".config('app.name'))
            ->attachment(function ($attachment) use ($fields) {
                $attachment->fields($fields);
            });
    }
}
