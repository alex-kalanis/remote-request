<?php

namespace RemoteRequest\Protocols\Fsp\Query;


use RemoteRequest\Protocols\Fsp;


/**
 * Class MakeDir
 * @package RemoteRequest\Protocols\Fsp\Query
 * Make remote directory
 */
class MakeDir extends AQuery
{
    protected $dirPath = '';

    protected function getCommand(): int
    {
        return Fsp::CC_MAKE_DIR;
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
