<?php

namespace NotificationChannels\Line;

class LineMessage
{
    /** @var string|null Line user id. */
    protected $to = null;

    /** @var string|null A user or app access token. */
    protected $from = null;

    /** @var string The text content of the message. */
    protected $content;

    /**
     * Create a new instance of LineMessage.
     *
     * @return static
     */
    public static function create(string $content = ''): self
    {
        return new static($content);
    }

    /**
     * Create a new instance of LineMessage.
     */
    public function __construct(string $content = '')
    {
        $this->content($content);
    }

    public function getTo(): ?string
    {
        return $this->to;
    }

    public function to(string $to): self
    {
        $this->to = $to;

        return $this;
    }

    public function getFrom(): ?string
    {
        return $this->from;
    }

    /**
     * Set the sender's access token.
     *
     * @return $this
     */
    public function from(string $accessToken): self
    {
        $this->from = $accessToken;

        return $this;
    }

    /**
     * Set the content of the Line message.
     * Supports GitHub flavoured markdown.
     *
     * @return $this
     */
    public function content(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get an array representation of the LineMessage.
     */
    public function toArray(): array
    {
        return array_filter([
            'messages' => [
                [
                    'type' => 'text',
                    'text' => $this->content,
                ],
            ],
        ]);
    }
}
