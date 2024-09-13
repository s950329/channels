<?php

namespace NotificationChannels\Line\Exceptions;

use GuzzleHttp\Exception\ClientException;

class CouldNotSendNotification extends \Exception
{
    /**
     * Thrown when there's a bad response from the LINE.
     *
     * @param  \GuzzleHttp\Exception\ClientException  $exception
     * @return static
     */
    public static function lineRespondedWithAnError(ClientException $exception)
    {
        $message = $exception->getResponse()->getBody();
        $code = $exception->getResponse()->getStatusCode();

        return new static("LINE responded with an error `{$code} - {$message}`");
    }

    /**
     * Thrown when we're unable to communicate with LINE.
     *
     * @param  \Exception  $exception
     * @return static
     */
    public static function couldNotCommunicateWithLine(\Exception $exception): self
    {
        return new static("The communication with LINE failed. Reason: {$exception->getMessage()}");
    }

    /**
     * Thrown when there's no bot token provided.
     */
    public static function lineMessageTokenNotProvided(string $message): self
    {
        return new self($message);
    }
}
