<?php

namespace kalanis\RemoteRequest\Protocols\Fsp\Answer;


/**
 * Class GetFile
 * @package kalanis\RemoteRequest\Protocols\Fsp\Answer
 * Process getting files
 */
class GetFile extends AAnswer
{
    protected int $seek = 0;
    protected string $load = '';

    public function process(): parent
    {
        $this->seek = $this->answer->getFilePosition();
        $this->load = substr($this->answer->getContent(), 0, -1);
        return $this;
    }

    public function getContent(): string
    {
        return $this->load;
    }

    public function getSeek(): int
    {
        return $this->seek;
    }
}
