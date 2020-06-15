<?php

namespace RemoteRequest\Protocols\Fsp\Query;

use RemoteRequest\Protocols\Fsp;

/**
 * Stats about file
 */
class Stat extends AQuery
{
    protected $filePath = '';

    protected function getCommand(): int
    {
        return Fsp::CC_STAT;
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