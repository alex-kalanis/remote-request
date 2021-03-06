<?php

namespace RemoteRequest\Protocols\Fsp\Query;

use RemoteRequest\Protocols\Fsp;

/**
 * Install file
 * Start file upload - this one will be saved
 * To stop just send Install with zero length
 *
 * Correct name for v3 should be Publish - it publish uploaded file (move it from temp to target)
 */
class Install extends AQuery
{
    protected $filePath = '';
    protected $timestamp = 0;

    protected function getCommand(): int
    {
        return Fsp::CC_INSTALL;
    }

    public function setFilePath(string $filePath): self
    {
        $this->filePath = $filePath;
        return $this;
    }

    public function setTimestamp(string $timestamp): self
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    protected function getFilePosition(): int
    {
        return isset($this->timestamp) ? strlen($this->getExtraData()) : 0;
    }

    protected function getData(): string
    {
        return $this->filePath . chr(0);
    }

    protected function getExtraData(): string
    {
        return ($this->timestamp) ? Fsp\Strings::filler($this->timestamp, 4) : '' ;
    }
}