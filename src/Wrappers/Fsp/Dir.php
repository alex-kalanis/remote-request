<?php

namespace RemoteRequest\Wrappers\Fsp;

use RemoteRequest;
use RemoteRequest\Protocols\Fsp as Protocol;

/**
 * Wrapper to plug FSP info into PHP - directory part
 */
class Dir extends AOperations
{
    protected $path = '';
    protected $seek = 0;

    public function close(): bool
    {
        $this->seek = 0;
        return true;
    }

    public function open(string $path, int $options): bool
    {
        $this->path = $path;
        return true;
    }

    /**
     * @return string
     * @throws RemoteRequest\RequestException
     */
    public function read(): string
    {
        $answer = $this->readDir();
        $this->seek++;
        return $answer->getFileName();
    }

    public function rewind(): bool
    {
        $this->seek = 0;
        return true;
    }

    /**
     * @param string $path
     * @param int $mode
     * @param int $options
     * @return bool
     * @throws RemoteRequest\RequestException
     */
    public function make(string $path, int $mode, int $options): bool
    {
        $mkDir = new Protocol\Query\MakeDir($this->runner->getQuery());
        $mkDir->setDirPath($this->parsePath($path));
        /** @var Protocol\Answer\Protection $answer */
        $answer = $this->runner->setActionQuery($mkDir)->process();
        if (!$answer instanceof Protocol\Answer\Protection) {
            throw new RemoteRequest\RequestException('Got something bad with mkdir. Class ' . get_class($answer));
        }
        // TODO: send protection data - set from $mode
        return true;
    }

    /**
     * @param string $pathFrom
     * @param string $pathTo
     * @return bool
     * @throws RemoteRequest\RequestException
     */
    public function rename(string $pathFrom, string $pathTo): bool
    {
        $rename = new Protocol\Query\Rename($this->runner->getQuery());
        $rename
            ->setFilePath($this->parsePath($pathFrom))
            ->setNewPath($this->parsePath($pathTo, false))
        ;
        $answer = $this->runner->setActionQuery($rename)->process();
        if (!$answer instanceof Protocol\Answer\Nothing) {
            throw new RemoteRequest\RequestException('Got something bad with rename. Class ' . get_class($answer));
        }
        return true;
    }

    /**
     * @param string $path
     * @param int $options
     * @return bool
     * @throws RemoteRequest\RequestException
     */
    public function remove(string $path, int $options): bool
    {
        $delDir = new Protocol\Query\DelDir($this->runner->getQuery());
        $delDir->setDirPath($this->parsePath($path));
        $answer = $this->runner->setActionQuery($delDir)->process();
        if (!$answer instanceof Protocol\Answer\Nothing) {
            throw new RemoteRequest\RequestException('Got something bad with rmdir. Class ' . get_class($answer));
        }
        return true;
    }

    /**
     * @param string $path
     * @param int $flags
     * @return array
     * @throws RemoteRequest\RequestException
     */
    public function stats(string $path, int $flags): array
    {
        // tohle bude jako readdir(), ale s tim rozdilem, ze se potrebuji vytahnout ta data, ne jmeno
        $parsedPath = $this->parsePath($path);
        $slashPos = strrpos('/', $parsedPath);
        if (false === $slashPos) {
            throw new RemoteRequest\RequestException('Bad parsed path: ' . $parsedPath);
        }
        $fileName = substr($parsedPath, $slashPos);
        $this->path = substr($parsedPath, 0, $parsedPath - 1);

        $infoAnswer = null;
//            $dirInfo = new Protocol\Query\GetProtection($this->runner->getQuery());
//            $dirInfo->setDirPath($this->path);
//            $infoAnswer = $this->runner->setActionQuery($dirInfo)->process();
//            if (!$infoAnswer instanceof Protocol\Answer\Protection) {
//                throw new RemoteRequest\RequestException('Got something bad with stat protection. Class ' . get_class($infoAnswer));
//            }

        $this->seek = 0;
        do {
            // doseekovali jsme na jmeno...
            $answer = $this->readDir();
            if ($answer->getFileName() == $fileName) {
                return [
                    0 => 0,
                    1 => 0,
                    2 => $this->parseMode($infoAnswer, $answer->getType()),
                    3 => 0,
                    4 => 0,
                    5 => 0,
                    6 => 0,
                    7 => $answer->getSize(),
                    8 => $answer->getTime(),
                    9 => $answer->getTime(),
                    10 => $answer->getTime(),
                    11 => -1,
                    12 => -1,
                ];
            }
            $this->seek++;
        } while ($this->seek < 65536);
        throw new RemoteRequest\RequestException('FSP path not found: ' . $path);
    }

    /**
     * @return Protocol\Answer\GetDir
     * @throws RemoteRequest\RequestException
     */
    protected function readDir(): Protocol\Answer\GetDir
    {
        $rdDir = new Protocol\Query\GetDir($this->runner->getQuery());
        $rdDir->setDirPath($this->parsePath($this->path))->setPosition($this->seek);
        $answer = $this->runner->setActionQuery($rdDir)->process();
        if (!$answer instanceof Protocol\Answer\GetDir) {
            throw new RemoteRequest\RequestException('Got something bad with mkdir. Class ' . get_class($answer));
        }
        return $answer;
    }

    /**
     * @param Protocol\Answer\Protection $info
     * @param int|null $mode
     * @return int
     * @throws RemoteRequest\RequestException
     * @link https://www.php.net/manual/en/function.stat.php
     */
    protected function parseMode(?Protocol\Answer\Protection $info, ?int $mode): int
    {
        switch ($mode) {
            case Protocol::RDTYPE_DIR:
                // recursion to load more about dir?
                return 0040755;
            case Protocol::RDTYPE_FILE:
                return 0100644;
            case Protocol::RDTYPE_LINK:
                return 0120644;
            case Protocol::RDTYPE_END:
                throw new RemoteRequest\RequestException('No more');
            default:
                return 0;
        }
    }
}
