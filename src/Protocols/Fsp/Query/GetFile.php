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
    protected $limit = 0;

    protected function getCommand(): int
    {
        return Fsp::CC_GET_FILE;
    }

    public function setFilePath(string $filePath): self
    {
        $this->filePath = $filePath;
        return $this;
    }

    public function setOffset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    public function setLimit(int $limit): self
    {
        $this->limit = $limit;
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

    protected function getExtraData(): string
    {
        return ($this->limit) ? Fsp\Strings::filler($this->limit, 2) : '' ;
    }
}