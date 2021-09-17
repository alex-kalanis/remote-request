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
    protected $path = '';
    protected $size = 0;
    protected $position = 0;
    protected $writeMode = false; // read - false, write - true

    /**
     * @param int $cast_as
     * @return resource|bool
     */
    public function stream_cast(int $cast_as)
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
                throw new RemoteRequest\RequestException('Got something bad with publishing. Class ' . get_class($answer));
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

    public function stream_lock(int $operation): bool
    {
        return false;
    }

    public function stream_metadata(string $path, int $option, $var): bool
    {
        return false;
    }

    /**
     * @param Dir $libDir
     * @param string $path
     * @param string $mode
     * @return bool
     * @throws RemoteRequest\RequestException
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
     * @return bool
     * @throws RemoteRequest\RequestException
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
        throw new RemoteRequest\RequestException('Got problematic mode: ' . $mode);
    }

    /**
     * @param int $count
     * @return string
     * @throws RemoteRequest\RequestException
     */
    public function stream_read(int $count): string
    {
        $readFile = new Protocol\Query\GetFile($this->runner->getQuery());
        $readFile->setFilePath($this->parsePath($this->path))->setOffset($this->position)->setLimit($count);
        $answer = $this->runner->setActionQuery($readFile)->process();
        if (!$answer instanceof Protocol\Answer\GetFile) {
            throw new RemoteRequest\RequestException('Got something bad with reading. Class ' . get_class($answer));
        }
        if ($answer->getSeek() != $this->position) {
            throw new RemoteRequest\RequestException(sprintf('Bad read seek. Want %d got %d ', $this->position, $answer->getSeek()));
        }
        $ret = $answer->getContent();
        $this->position += strlen($ret);
        return $ret;
    }

    public function stream_seek(int $offset, int $whence = SEEK_SET): bool
    {
        switch ($whence) {
            case SEEK_SET:
                if ($offset < $this->size && $offset >= 0) {
                    $this->position = $offset;
                    return true;
                } else {
                    return false;
                }
                break;

            case SEEK_CUR:
                if ($offset >= 0) {
                    $this->position += $offset;
                    return true;
                } else {
                    return false;
                }
                break;

            case SEEK_END:
                if ($this->size + $offset >= 0) {
                    $this->position = $this->size + $offset;
                    return true;
                } else {
                    return false;
                }
                break;

            default:
                return false;
        }
    }

    public function stream_set_option(int $option, int $arg1, int $arg2): bool
    {
        return false;
    }

    /**
     * @param Dir $libDir
     * @return array
     * @throws RemoteRequest\RequestException
     */
    public function stream_stat(Dir $libDir): array
    {
        return $libDir->stats($this->path, 0);
    }

    public function stream_tell(): int
    {
        return $this->position;
    }

    public function stream_truncate(int $new_size): bool
    {
        return false;
    }

    /**
     * @param string $data
     * @return int
     * @throws RemoteRequest\RequestException
     */
    public function stream_write(string $data): int
    {
        if (!$this->writeMode) {
            throw new RemoteRequest\RequestException('File not open for writing!');
        }
        $upFile = new Protocol\Query\Upload($this->runner->getQuery());
        $upFile->setFilePath($this->parsePath($this->path))->setOffset($this->position)->setData($data);
        $answer = $this->runner->setActionQuery($upFile)->process();
        if (!$answer instanceof Protocol\Answer\Upload) {
            throw new RemoteRequest\RequestException('Got something bad with uploading. Class ' . get_class($answer));
        }
        $dataLen = strlen($data);
        if ($answer->getSeek() != $this->position + $dataLen) {
            throw new RemoteRequest\RequestException(sprintf('Bad write seek. Want %d got %d ', $this->position + $dataLen, $answer->getSeek()));
        }
        $this->position = $answer->getSeek();
        return $dataLen;
    }

    /**
     * @param string $path
     * @return bool
     * @throws RemoteRequest\RequestException
     */
    public function unlink(string $path): bool
    {
        $delFile = new Protocol\Query\DelFile($this->runner->getQuery());
        $delFile->setFilePath($this->parsePath($path));
        $answer = $this->runner->setActionQuery($delFile)->process();
        if (!$answer instanceof Protocol\Answer\Nothing) {
            throw new RemoteRequest\RequestException('Got something bad with unlink. Class ' . get_class($answer));
        }
        return true;
    }
}
