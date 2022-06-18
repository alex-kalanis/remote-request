<?php

namespace kalanis\RemoteRequest\Protocols\Dummy;


use kalanis\RemoteRequest\Interfaces;
use kalanis\RemoteRequest\Protocols\Helper;


/**
 * Class Query
 * @package RemoteRequest\Protocols\Dummy
 * Simple query to remote source
 */
class Query implements Interfaces\IQuery
{
    /** @var string */
    public $body = '';
    /** @var int|null */
    public $maxLength = null;

    public function setExpectedAnswerSize(?int $maxLength = null): self
    {
        $this->maxLength = !is_null($maxLength) ? $maxLength : $this->maxLength;
        return $this;
    }

    public function getMaxAnswerLength(): ?int
    {
        return $this->maxLength;
    }

    public function getData()
    {
        $storage = Helper::getMemStorage();
        fwrite($storage, $this->body);
        rewind($storage);
        return $storage;
    }
}
