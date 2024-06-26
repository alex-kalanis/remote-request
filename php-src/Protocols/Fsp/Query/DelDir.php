<?php

namespace kalanis\RemoteRequest\Protocols\Fsp\Query;


use kalanis\RemoteRequest\Protocols\Fsp;


/**
 * Class DelDir
 * @package kalanis\RemoteRequest\Protocols\Fsp\Query
 * Delete remote directory
 */
class DelDir extends AQuery
{
    protected string $dirPath = '';

    protected function getCommand(): int
    {
        return Fsp::CC_DEL_DIR;
    }

    public function setDirPath(string $filePath): self
    {
        $this->dirPath = $filePath;
        return $this;
    }

    protected function getData(): string
    {
        return $this->dirPath . chr(0);
    }
}
