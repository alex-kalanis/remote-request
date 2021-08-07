<?php

namespace RemoteRequest\Protocols\Fsp\Query;


use RemoteRequest\Protocols\Fsp;


/**
 * Class DelFile
 * @package RemoteRequest\Protocols\Fsp\Query
 * Delete remote file
 */
class DelFile extends AQuery
{
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
