<?php

namespace RemoteRequest\Protocols\Fsp\Answer;

use RemoteRequest\Protocols\Fsp;

/**
 * Process Get Directory
 */
class GetDir extends AAnswer
{
    protected $position = 0;
    protected $name = '';
    protected $time = 0;
    protected $size = 0;
    protected $type = null;

    public function process(): parent
    {
        $this->position = $this->answer->getFilePosition();
        $data = $this->answer->getContent();
        $this->time = Fsp\Strings::mb_ord(substr($data, 0, 4));
        $this->size = Fsp\Strings::mb_ord(substr($data, 4, 4));
        $this->type = Fsp\Strings::mb_ord($data[8]);
        $this->name = substr($data, 9, -1);
        return $this;
    }

    public function getFileName(): string
    {
        return $this->name;
    }

    public function getTime(): int
    {
        return $this->time;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getType(): ?int
    {
        return $this->type;
    }
}