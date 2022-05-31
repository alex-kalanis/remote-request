<?php

namespace kalanis\RemoteRequest\Protocols\Dummy;


/**
 * Class Answer
 * @package kalanis\RemoteRequest\Protocols\Dummy
 * Process server's answer - raw edition
 */
class Answer
{
    /** @var resource|null */
    protected $body = null;

    /**
     * @param resource|string|null $message
     * @return $this
     */
    public function setResponse($message): self
    {
        $this->body = $message;
        return $this;
    }

    public function getContent(): string
    {
        return $this->body ? stream_get_contents($this->body, -1, 0) : '';
    }
}
