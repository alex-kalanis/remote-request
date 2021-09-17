<?php

namespace kalanis\RemoteRequest\Protocols\Fsp\Query;


use kalanis\RemoteRequest\Protocols\Fsp;


/**
 * Class GetDir
 * @package kalanis\RemoteRequest\Protocols\Fsp\Query
 * Want directory listing
 */
class GetDir extends AQuery
{
    protected $dirPath = '';
    protected $position = 0;

    protected function getCommand(): int
    {
        return Fsp::CC_GET_DIR;
    }

    public function setDirPath(string $dirPath): self
    {
        $this->dirPath = $dirPath;
        return $this;
    }

    public function setPosition(string $position): self
    {
        $this->position = $position;
        return $this;
    }

    protected function getFilePosition(): int
    {
        return $this->position;
    }

    protected function getData(): string
    {
        return $this->dirPath . chr(0);
    }
}
