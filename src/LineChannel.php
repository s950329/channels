<?php

namespace NotificationChannels\Line;

use Exception;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Response;
use Illuminate\Notifications\Notification;
use NotificationChannels\Line\Exceptions\CouldNotSendNotification;

class LineChannel
{
    /** @var \NotificationChannels\Line\Line The HTTP client instance. */
    private $line;

    public function __construct(Line $line)
    {
        $this->line = $line;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     *
     * @throws \NotificationChannels\Line\Exceptions\CouldNotSendNotification
     */
    public function send($notifiable, Notification $notification)
    {
        /** @var \NotificationChannels\Line\LineMessage $message */
        $message = $notification->toLine($notifiable);

        $to = $message->getTo() ?: $notifiable->routeNotificationFor('line');
        if ($to === null) {
            return null;
        }

        if ($message->getFrom()) {
            $this->line->setToken($message->getFrom());
        }

        try {
            $response = $this->sendMessage($to, $message);
        } catch (ClientException $exception) {
            throw CouldNotSendNotification::lineRespondedWithAnError($exception);
        } catch (Exception $exception) {
            throw CouldNotSendNotification::couldNotCommunicateWithLine($exception);
        }

        return $response instanceof Response ? json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR) : $response;
    }

    /**
     * @param  string  $to
     * @param  \NotificationChannels\LINE\LINEMessage  $message
     * @return void
     */
    private function sendMessage(string $to, LineMessage $message): void
    {
        $this->line->sendMessage($to, $message->toArray());
    }
}
