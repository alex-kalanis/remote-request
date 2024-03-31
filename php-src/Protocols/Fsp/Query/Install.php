<?php

namespace kalanis\RemoteRequest\Protocols\Fsp\Query;


use kalanis\RemoteRequest\Protocols\Fsp;


/**
 * Class Install
 * @package kalanis\RemoteRequest\Protocols\Fsp\Query
 * Install file
 * Start file upload - this one will be saved
 * To stop just send Install with zero length
 *
 * Correct name for v3 should be Publish - it publish uploaded file (move it from temp to target)
 */
class Install extends AQuery
{
    protected string $filePath = '';
    protected int $timestamp = 0;

    protected function getCommand(): int
    {
        return Fsp::CC_INSTALL;
    }

    public function setFilePath(string $filePath): self
    {
        $this->filePath = $filePath;
        return $this;
    }

    public function setTimestamp(int $timestamp): self
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    protected function getFilePosition(): int
    {
        return strlen($this->getExtraData());
    }

    protected function getData(): string
    {
        return $this->filePath . chr(0);
    }

    protected function getExtraData(): string
    {
        return !empty($this->timestamp) ? Fsp\Strings::filler($this->timestamp, 4) : '' ;
    }
}
