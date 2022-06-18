<?php

namespace kalanis\RemoteRequest\Wrappers\Fsp;


use kalanis\RemoteRequest;
use kalanis\RemoteRequest\Protocols\Fsp as Protocol;


/**
 * Class Dir
 * @package kalanis\RemoteRequest\Wrappers\Fsp
 * Wrapper to plug FSP info into PHP - directory part
 */
class Dir extends AOperations
{
    /** @var Protocol\Answer\GetDir\FileInfo[] */
    protected $files = [];
    /** @var string */
    protected $path = '';
    /** @var int */
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
     * @throws RemoteRequest\RequestException
     * @return string|bool
     */
    public function read()
    {
        $part = $this->readFiles($this->path);
        if (empty($part)) {
            return false;
        }
        return $part->getFilename();
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
     * @throws RemoteRequest\RequestException
     * @return bool
     */
    public function make(string $path, int $mode, int $options): bool
    {
        $mkDir = new Protocol\Query\MakeDir($this->runner->getQuery());
        $mkDir->setDirPath($this->parsePath($path));
        $answer = $this->runner->setActionQuery($mkDir)->process();
        if (!$answer instanceof Protocol\Answer\Protection) {
            throw new RemoteRequest\RequestException($this->lang->rrFspBadMkDir(get_class($answer)));
        }
        // TODO: send protection data - set from $mode
//        $this->rights($path, $mode[0], true);
        return true;
    }

    /**
     * @param string $path
     * @param string $right
     * @param bool $allow
     * @throws RemoteRequest\RequestException
     * @return bool
     */
    public function rights(string $path, string $right, bool $allow): bool
    {
        $protect = new Protocol\Query\SetProtection($this->runner->getQuery());
        $protect
            ->setDirPath($this->parsePath($path))
            ->setOperation($right)
            ->allowOperation($allow)
        ;
        $answer = $this->runner->setActionQuery($protect)->process();
        if (!$answer instanceof Protocol\Answer\Protection) {
            throw new RemoteRequest\RequestException($this->lang->rrFspBadProtection(get_class($answer)));
        }
        return true;
    }

    /**
     * @param string $pathFrom
     * @param string $pathTo
     * @throws RemoteRequest\RequestException
     * @return bool
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
            throw new RemoteRequest\RequestException($this->lang->rrFspBadRename(get_class($answer)));
        }
        return true;
    }

    /**
     * @param string $path
     * @param int $options
     * @throws RemoteRequest\RequestException
     * @return bool
     */
    public function remove(string $path, int $options): bool
    {
        $delDir = new Protocol\Query\DelDir($this->runner->getQuery());
        $delDir->setDirPath($this->parsePath($path));
        $answer = $this->runner->setActionQuery($delDir)->process();
        if (!$answer instanceof Protocol\Answer\Nothing) {
            throw new RemoteRequest\RequestException($this->lang->rrFspBadRmDir(get_class($answer)));
        }
        return true;
    }

    /**
     * @param string $path
     * @param int $flags
     * @throws RemoteRequest\RequestException
     * @return array<int, int>
     */
    public function stats(string $path, int $flags): array
    {
        $parsedPath = $this->parsePath($path);
        $slashPos = strrpos($path, '/');
        if (false === $slashPos) {
            throw new RemoteRequest\RequestException($this->lang->rrFspBadParsedPath($parsedPath));
        }
        $fileName = substr($path, $slashPos + 1);
        $dirPath = substr($path, 0, $slashPos);

        $infoAnswer = null;
//        $dirInfo = new Protocol\Query\GetProtection($this->runner->getQuery());
//        $dirInfo->setDirPath($dirPath);
//        $infoAnswer = $this->runner->setActionQuery($dirInfo)->process();
//        if (!$infoAnswer instanceof Protocol\Answer\Protection) {
//            throw new RemoteRequest\RequestException('Got something bad with stat protection. Class ' . get_class($infoAnswer));
//        }

        while ($fileInfo = $this->readFiles($dirPath)) {
            // seek into the name...
            if ($fileInfo->getFileName() == $fileName) {
                return [
                    0 => 0,
                    1 => 0,
                    2 => $this->parseMode($infoAnswer, $fileInfo->getOrigType()),
                    3 => 0,
                    4 => 0,
                    5 => 0,
                    6 => 0,
                    7 => $fileInfo->getSize(),
                    8 => $fileInfo->getATime(),
                    9 => $fileInfo->getMTime(),
                    10 => $fileInfo->getCTime(),
                    11 => -1,
                    12 => -1,
                ];
            }
        }
        throw new RemoteRequest\RequestException($this->lang->rrFspPathNotFound($path));
    }

    /**
     * @param string $path
     * @throws RemoteRequest\RequestException
     * @return Protocol\Answer\GetDir\FileInfo|null
     */
    public function readFiles(string $path): ?Protocol\Answer\GetDir\FileInfo
    {
        if (false === next($this->files)) {
            $this->files = $this->readDir($path)->getFiles();
            reset($this->files);
            $this->seek++;
        }
        $file = current($this->files);
        return false !== $file ? $file : null;
    }

    /**
     * @param string $path
     * @throws RemoteRequest\RequestException
     * @return Protocol\Answer\GetDir
     */
    protected function readDir(string $path): Protocol\Answer\GetDir
    {
        $rdDir = new Protocol\Query\GetDir($this->runner->getQuery());
        $rdDir->setDirPath($this->parsePath($path))->setPosition($this->seek);
        $answer = $this->runner->setActionQuery($rdDir)->process();
        if (!$answer instanceof Protocol\Answer\GetDir) {
            throw new RemoteRequest\RequestException($this->lang->rrFspBadMkDir(get_class($answer)));
        }
        $answer->process();
        return $answer;
    }

    /**
     * @param Protocol\Answer\Protection $info
     * @param int|null $mode
     * @throws RemoteRequest\RequestException
     * @return int
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
                throw new RemoteRequest\RequestException($this->lang->rrFspFileCannotCont());
            default:
                return 0;
        }
    }
}
