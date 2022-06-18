<?php

namespace kalanis\RemoteRequest\Protocols\Fsp\Query;


use kalanis\RemoteRequest\Protocols\Fsp;


/**
 * Class Rename
 * @package kalanis\RemoteRequest\Protocols\Fsp\Query
 * Rename file on remote
 */
class Rename extends AQuery
{
    /** @var string */
    protected $filePath = '';
    /** @var string */
    protected $newPath = '';

    protected function getCommand(): int
    {
        return Fsp::CC_RENAME;
    }

    public function setFilePath(string $filePath): self
    {
        $this->filePath = $filePath;
        return $this;
    }

    public function setNewPath(string $newPath): self
    {
        $this->newPath = $newPath;
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
        return $this->newPath . chr(0);
    }
}
