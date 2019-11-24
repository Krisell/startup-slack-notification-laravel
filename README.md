# Startup Slack Notification for Laravel
This package adds a command, `startup-notification:slack` which sends a startup-notification to slack to inform you when the server is started. This is useful during a deploy to get a notification when the deployment has been completed, during horizontal scaling to be notified when new servers are started, and to be notified if your server is restarted for unexpected reasons.

## Installation
Add the package to your Laravel project.

```bash
composer require krisell/startup-slack-notification-laravel
```

The package is configured for automatic discovery, so unless you have other settings, you do not need to manually add the service provider.

## Usage
Add your slack hook, either to the .env-variable `STARTUP_SLACK_HOOK=` or by publishing the configuration (using `php artisan vendor:publish`) and setting the `slack_hook` config value.

Run the command `startup-notification:slack` during server boot.

## Testing an actual message
One of the tests is prepared to send an acutal slack message. To run this test, create a `.hook`-file in the `/tests` directory and set the contents to a slack hook URL.

All other tests mocks the actual sending of the notification, and instead makes assertions on the contents before transmission.

## Licence
MIT

## Author
Martin Krisell (martin.krisell@gmail.com)
