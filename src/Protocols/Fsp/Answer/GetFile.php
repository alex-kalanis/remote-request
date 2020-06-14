<?php

namespace RemoteRequest\Protocols\Fsp\Answer;

/**
 * Process getting files
 */
class GetFile extends AAnswer
{
    protected $seek = 0;
    protected $load = '';

    public function process(): parent
    {
        $this->seek = $this->answer->getFilePosition();
        $this->load = $this->answer->getContent();
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