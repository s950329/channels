<?php

declare(strict_types=1);

namespace NotificationChannels\Line;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;
use NotificationChannels\Line\Exceptions\CouldNotSendNotification;

final class Line
{
    /** @var \GuzzleHttp\Client */
    private $http;

    /** @var string */
    private $url;

    /** @var string */
    private $token;

    /** @var string|null */
    private $defaultChannel;

    /**
     * @param  string  $url
     * @param  string|null  $defaultChannel
     * @return void
     */
    public function __construct(HttpClient $http, string $token = null)
    {
        $this->http = $http;
        $this->token = $token;
    }

    /**
     * Returns Line token.
     */
    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Send text message.
     *
     * <code>
     * $params = [
     *   'chat_id'                  => '',
     *   'text'                     => '',
     *   'parse_mode'               => '',
     *   'disable_web_page_preview' => '',
     *   'disable_notification'     => '',
     *   'reply_to_message_id'      => '',
     *   'reply_markup'             => '',
     * ];
     * </code>
     *
     * @see https://core.telegram.org/bots/api#sendmessage
     *
     * @throws CouldNotSendNotification
     */
    public function sendMessage(string $to, array $params): ?ResponseInterface
    {
        return $this->sendRequest($to, $params);
    }

    /**
     * Perform a simple post request.
     */
    private function post(string $url, array $options): void
    {
        $this->http->post($url, $options);
    }

    /**
     * Send an API request and return response.
     *
     *
     * @throws CouldNotSendNotification
     */
    protected function sendRequest(string $to, array $params): ?ResponseInterface
    {
        if (blank($this->token)) {
            throw CouldNotSendNotification::lineMessageTokenNotProvided('You must provide your line message token to make any API requests.');
        }

        $url = 'https://api.line.me/v2/bot/message/push';

        try {
            return $this->post($url, [
                'json' => array_merge($params, [
                    'to' => $to,
                ]),
                'headers' => [
                    'Authorization' => 'Bearer '.$this->token,
                ],
            ]);
        } catch (ClientException $exception) {
            throw CouldNotSendNotification::lineRespondedWithAnError($exception);
        } catch (\Exception $exception) {
            throw CouldNotSendNotification::couldNotCommunicateWithLine($exception);
        }
    }
}
