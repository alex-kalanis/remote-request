<?php

namespace kalanis\RemoteRequest\Protocols\Fsp\Query;


use kalanis\RemoteRequest\Protocols\Fsp;


/**
 * Class Stat
 * @package kalanis\RemoteRequest\Protocols\Fsp\Query
 * Stats about file
 */
class Stat extends AQuery
{
    protected string $filePath = '';

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
