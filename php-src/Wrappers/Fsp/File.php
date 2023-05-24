<?php

namespace kalanis\RemoteRequest\Wrappers\Fsp;


use kalanis\RemoteRequest;
use kalanis\RemoteRequest\Protocols\Fsp as Protocol;


/**
 * Class File
 * @package kalanis\RemoteRequest\Wrappers\Fsp
 * Wrapper to plug FSP info into PHP - files part
 */
class File extends AOperations
{
    /** @var string */
    protected $path = '';
    /** @var int */
    protected $size = 0;
    /** @var int */
    protected $position = 0;
    /**
     * @var bool
     * read - false, write - true
     */
    protected $writeMode = false;

    /**
     * @param int $cast_as
     * @return resource|bool
     */
    public function stream_cast(/** @scrutinizer ignore-unused */ int $cast_as)
    {
        return false;
    }

    /**
     * @throws RemoteRequest\RequestException
     */
    public function stream_close(): void
    {
        if ($this->writeMode) {
            $inFile = new Protocol\Query\Install($this->runner->getQuery());
            $inFile->setFilePath($this->parsePath($this->path));
            $answer = $this->runner->setActionQuery($inFile)->process();
            if (!$answer instanceof Protocol\Answer\Nothing) {
                throw new RemoteRequest\RequestException($this->getRRLang()->rrFspBadResponsePublish(get_class($answer)));
            }
        }
    }

    public function stream_eof(): bool
    {
        return (!$this->writeMode) && ($this->position >= $this->size);
    }

    public function stream_flush(): bool
    {
        return false;
    }

    public function stream_lock(/** @scrutinizer ignore-unused */ int $operation): bool
    {
        return false;
    }

    /**
     * @param string $path
     * @param int $option
     * @param mixed $var
     * @return bool
     */
    public function stream_metadata(/** @scrutinizer ignore-unused */ string $path, /** @scrutinizer ignore-unused */ int $option, $var): bool
    {
        return false;
    }

    /**
     * @param Dir $libDir
     * @param string $path
     * @param string $mode
     * @throws RemoteRequest\RequestException
     * @return bool
     */
    public function stream_open(Dir $libDir, string $path, string $mode): bool
    {
        $this->path = $path;
        $this->writeMode = $this->parseWriteMode($mode);

        if (!$this->writeMode) {
            $stat = $this->stream_stat($libDir);
            $this->size = $stat[7]; // stats - max available size
        }
        $this->position = 0;
        return true;
    }

    /**
     * @param string $mode
     * @throws RemoteRequest\RequestException
     * @return bool
     */
    protected function parseWriteMode(string $mode): bool
    {
        $mod = strtolower(substr(strtr($mode, ['+' => '', 'b' => '', 'e' => '']), 0, 1));
        if ('r' == $mod) {
            return false;
        }
        if (in_array($mod, ['w', 'a', 'x', 'c'])) {
            return true;
        }
        throw new RemoteRequest\RequestException($this->getRRLang()->rrFspBadFileMode($mode));
    }

    /**
     * @param int $count
     * @throws RemoteRequest\RequestException
     * @return string
     */
    public function stream_read(int $count): string
    {
        $readFile = new Protocol\Query\GetFile($this->runner->getQuery());
        $readFile->setFilePath($this->parsePath($this->path))->setOffset($this->position)->setLimit($count);
        $answer = $this->runner->setActionQuery($readFile)->process();
        if (!$answer instanceof Protocol\Answer\GetFile) {
            throw new RemoteRequest\RequestException($this->getRRLang()->rrFspBadResponseRead(get_class($answer)));
        }
        if ($answer->getSeek() != $this->position) {
            throw new RemoteRequest\RequestException($this->getRRLang()->rrFspReadWrongSeek($this->position, $answer->getSeek()));
        }
        $ret = $answer->getContent();
        $this->position += strlen($ret);
        return $ret;
    }

    public function stream_seek(int $offset, int $whence = SEEK_SET): bool
    {
        switch ($whence) {
            case SEEK_SET:
                if ($offset < $this->size && 0 <= $offset) {
                    $this->position = $offset;
                    return true;
                } else {
                    return false;
                }

            case SEEK_CUR:
                if (0 <= $offset) {
                    $this->position += $offset;
                    return true;
                } else {
                    return false;
                }

            case SEEK_END:
                if (0 <= $this->size + $offset) {
                    $this->position = $this->size + $offset;
                    return true;
                } else {
                    return false;
                }

            default:
                return false;
        }
    }

    public function stream_set_option(/** @scrutinizer ignore-unused */ int $option, /** @scrutinizer ignore-unused */ int $arg1, /** @scrutinizer ignore-unused */ int $arg2): bool
    {
        return false;
    }

    /**
     * @param Dir $libDir
     * @throws RemoteRequest\RequestException
     * @return array<int, int>
     */
    public function stream_stat(Dir $libDir): array
    {
        return $libDir->stats($this->path, 0);
    }

    public function stream_tell(): int
    {
        return $this->position;
    }

    public function stream_truncate(/** @scrutinizer ignore-unused */ int $new_size): bool
    {
        return false;
    }

    /**
     * @param string $data
     * @throws RemoteRequest\RequestException
     * @return int
     */
    public function stream_write(string $data): int
    {
        if (!$this->writeMode) {
            throw new RemoteRequest\RequestException($this->getRRLang()->rrFspFileCannotWrite());
        }
        $upFile = new Protocol\Query\Upload($this->runner->getQuery());
        $upFile->setFilePath($this->parsePath($this->path))->setOffset($this->position)->setData($data);
        $answer = $this->runner->setActionQuery($upFile)->process();
        if (!$answer instanceof Protocol\Answer\Upload) {
            throw new RemoteRequest\RequestException($this->getRRLang()->rrFspBadResponseUpload(get_class($answer)));
        }
        $dataLen = strlen($data);
        if ($answer->getSeek() != $this->position + $dataLen) {
            throw new RemoteRequest\RequestException($this->getRRLang()->rrFspWriteWrongSeek($this->position + $dataLen, $answer->getSeek()));
        }
        $this->position = $answer->getSeek();
        return $dataLen;
    }

    /**
     * @param string $path
     * @throws RemoteRequest\RequestException
     * @return bool
     */
    public function unlink(string $path): bool
    {
        $delFile = new Protocol\Query\DelFile($this->runner->getQuery());
        $delFile->setFilePath($this->parsePath($path));
        $answer = $this->runner->setActionQuery($delFile)->process();
        if (!$answer instanceof Protocol\Answer\Nothing) {
            throw new RemoteRequest\RequestException($this->getRRLang()->rrFspBadResponseUnlink(get_class($answer)));
        }
        return true;
    }
}
