<?php

namespace kalanis\RemoteRequest\Protocols\Fsp\Query;


use kalanis\RemoteRequest\Protocols\Fsp;


/**
 * Class DelFile
 * @package kalanis\RemoteRequest\Protocols\Fsp\Query
 * Delete remote file
 */
class DelFile extends AQuery
{
    /** @var string */
    protected $filePath = '';

    protected function getCommand(): int
    {
        return Fsp::CC_DEL_FILE;
    }

    public function setFilePath(string $filePath): self
    {
        $this->filePath = $filePath;
        return $this;
    }

    protected function getData(): string
    {
        return $this->filePath . chr(0);
    }
}
