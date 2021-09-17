<?php

namespace kalanis\RemoteRequest\Protocols\Fsp\Answer;


/**
 * Class Upload
 * @package kalanis\RemoteRequest\Protocols\Fsp\Answer
 * Process uploading files
 */
class Upload extends AAnswer
{
    protected $seek = 0;

    public function process(): parent
    {
        $this->seek = $this->answer->getFilePosition();
        return $this;
    }

    public function getSeek(): int
    {
        return $this->seek;
    }
}
