<?php

namespace kalanis\RemoteRequest\Protocols\Dummy;


/**
 * Class Answer
 * @package kalanis\RemoteRequest\Protocols\Dummy
 * Process server's answer - raw edition
 */
class Answer
{
    /** @var resource|string|null */
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
        return is_null($this->body)
            ? ''
            : (
                is_resource($this->body)
                ? strval(stream_get_contents($this->body, -1, 0))
                : strval($this->body)
            )
        ;
    }
}
