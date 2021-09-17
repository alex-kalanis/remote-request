<?php

namespace kalanis\RemoteRequest\Protocols\Fsp\Query;


use kalanis\RemoteRequest\Protocols\Fsp;


/**
 * Class Upload
 * @package kalanis\RemoteRequest\Protocols\Fsp\Query
 * Send file part
 */
class Upload extends AQuery
{
    protected $filePath = '';
    protected $data = '';
    protected $offset = 0;

    protected function getCommand(): int
    {
        return Fsp::CC_UP_LOAD;
    }

    public function setFilePath(string $filePath): self
    {
        $this->filePath = $filePath;
        return $this;
    }

    public function setData(string $data): self
    {
        $this->data = $data;
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
        return $this->data;
    }

    protected function getExtraData(): string
    {
        return $this->filePath . chr(0);
    }
}
