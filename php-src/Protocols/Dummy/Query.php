<?php

namespace kalanis\RemoteRequest\Protocols\Dummy;


use kalanis\RemoteRequest\Interfaces;
use kalanis\RemoteRequest\Protocols\Helper;
use kalanis\RemoteRequest\RequestException;


/**
 * Class Query
 * @package RemoteRequest\Protocols\Dummy
 * Simple query to remote source
 */
class Query implements Interfaces\IQuery
{
    public string $body = '';
    public ?int $maxLength = null;

    public function setExpectedAnswerSize(?int $maxLength = null): self
    {
        $this->maxLength = !is_null($maxLength) ? $maxLength : $this->maxLength;
        return $this;
    }

    public function getMaxAnswerLength(): ?int
    {
        return $this->maxLength;
    }

    /**
     * @throws RequestException
     * @return resource
     */
    public function getData()
    {
        $storage = Helper::getMemStorage();
        fwrite($storage, $this->body);
        rewind($storage);
        return $storage;
    }
}
