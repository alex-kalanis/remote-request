<?php

namespace kalanis\RemoteRequest\Protocols\Fsp\Answer;


use kalanis\RemoteRequest\Protocols\Fsp;


/**
 * Class Stats
 * @package kalanis\RemoteRequest\Protocols\Fsp\Answer
 * Process stats answer
 */
class Stats extends AAnswer
{
    /** @var int */
    protected $time = 0;
    /** @var int */
    protected $size = 0;
    /** @var int */
    protected $type = 0;

    public function process(): parent
    {
        $data = $this->answer->getContent();
        $this->time = Fsp\Strings::mb_ord(substr($data, 0, 4));
        $this->size = Fsp\Strings::mb_ord(substr($data, 4, 4));
        $this->type = Fsp\Strings::mb_ord($data[8]);
        return $this;
    }

    public function getTime(): int
    {
        return $this->time;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getType(): int
    {
        return $this->type;
    }
}
