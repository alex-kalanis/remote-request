<?php

namespace RemoteRequest\Protocols\Fsp\Query;

use RemoteRequest\Protocols\Fsp;

/**
 * Delete remote directory
 */
class DelDir extends AQuery
{
    protected $dirPath = '';

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