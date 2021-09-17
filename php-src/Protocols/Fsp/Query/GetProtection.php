<?php

namespace kalanis\RemoteRequest\Protocols\Fsp\Query;


use kalanis\RemoteRequest\Protocols\Fsp;


/**
 * Class GetProtection
 * @package kalanis\RemoteRequest\Protocols\Fsp\Query
 * Get dir protection details
 */
class GetProtection extends AQuery
{
    protected $dirPath = '';

    protected function getCommand(): int
    {
        return Fsp::CC_GET_PRO;
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
