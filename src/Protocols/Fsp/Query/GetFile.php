<?php

namespace RemoteRequest\Protocols\Fsp\Query;

use RemoteRequest\Protocols\Fsp;

/**
 * Want file part
 */
class GetFile extends AQuery
{
    protected $filePath = '';
    protected $offset = 0;

    protected function getCommand(): int
    {
        return Fsp::CC_GET_FILE;
    }

    public function setFilePath(string $filePath): self
    {
        $this->filePath = $filePath;
        return $this;
    }

    public function setOffset(string $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    protected function getFilePosition(): int
    {
        return $this->offset;
    }

    protected function getData(): string
    {
        return $this->filePath . chr(0);
    }
}