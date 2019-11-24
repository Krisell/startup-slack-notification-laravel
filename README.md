# Startup Slack Notification for Laravel
This package adds a command, `startup-notification:slack` which sends a startup-notification to slack to inform you when the server is started. This is useful during a deploy to get a notification when the deployment has been completed, during horizontal scaling to be notified when new servers are started, and to be notified if your server is restarted for unexpected reasons.

## Installation
Add the package to your Laravel project.

```bash
composer require krisell/startup-slack-notification-laravel
```

The package is configured for automatic discovery, so unless you have other settings, you do not need to manually add the service provider.

## Usage
Run the command `startup-notification:slack` during server boot.

## Licence
MIT

## Author
Martin Krisell (martin.krisell@gmail.com)
