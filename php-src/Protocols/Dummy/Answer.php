<?php

namespace kalanis\RemoteRequest\Protocols\Dummy;


/**
 * Class Answer
 * @package kalanis\RemoteRequest\Protocols\Dummy
 * Process server's answer - raw edition
 */
class Answer
{
    protected $body = '';

    public function setResponse(string $message)
    {
        $this->body = $message;
        return $this;
    }

    public function getContent(): string
    {
        return (string)$this->body;
    }
}