<?php

namespace NotificationChannels\Line;

use GuzzleHttp\Client as HttpClient;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;

class LineServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     */
    public function register(): void
    {
        $this->app->bind(Line::class, static fn () => new Line(
            app(HttpClient::class),
            config('services.line_message_api.token'),
        ));

        Notification::resolved(static function (ChannelManager $service) {
            $service->extend('line', static fn ($app) => $app->make(LineChannel::class));
        });
    }
}
