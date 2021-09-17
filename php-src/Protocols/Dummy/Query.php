<?php

namespace kalanis\RemoteRequest\Protocols\Dummy;


use kalanis\RemoteRequest\Connection;


/**
 * Class Query
 * @package RemoteRequest\Protocols\Dummy
 * Simple query to remote source
 */
class Query implements Connection\IQuery
{
    public $body = '';
    public $maxLength = null;

    public function setExpectedAnswerSize(?int $maxLength = null)
    {
        $this->maxLength = !is_null($maxLength) ? $maxLength : $this->maxLength;
        return $this;
    }

    public function getMaxAnswerLength(): ?int
    {
        return $this->maxLength;
    }

    public function getData(): string
    {
        return (string)$this->body;
    }
}
