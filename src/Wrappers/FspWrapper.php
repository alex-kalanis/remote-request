<?php

namespace RemoteRequest\Wrappers;

use RemoteRequest;
use RemoteRequest\Protocols\Fsp;

/**
 * Wrapper to plug FSP info into PHP
 * - for direct call fsp via php - the connection itself is in libraries outside
 * @link https://www.php.net/manual/en/class.streamwrapper.php
 * @link https://www.php.net/manual/en/stream.streamwrapper.example-1.php
 */
class FspWrapper
{
    /* Properties */
    /** @var resource */
    protected $context;
    protected $position;
    protected $varname;

    protected $key = null;
    protected $sequence = [];
    protected $wrapper = null;
    protected $processor = null;
    protected $query = null;
    protected $answer = null;

    /* Methods */
    public function __construct()
    {
        $this->wrapper = AWrapper::getWrapper(AWrapper::SCHEMA_UDP);
        $this->processor = new RemoteRequest\Connection\Processor(new RemoteRequest\Pointers\Socket());
        $this->query = new Fsp\Query();
        $this->answer = new Fsp\Answer();
    }

    public function __destruct()
    {
        if (isset($this->key)) {
            $this->sendBye();
        }
    }

    public function dir_closedir(): bool
    {
    }

    public function dir_opendir(string $path, int $options): bool
    {
    }

    public function dir_readdir(): string
    {
    }

    public function dir_rewinddir(): bool
    {
    }

    /**
     * @param string $path
     * @param int $mode
     * @param int $options
     * @return bool
     * @throws RemoteRequest\RequestException
     */
    public function mkdir(string $path, int $mode, int $options): bool
    {
        // create dir
        $mkDir = new Fsp\Query\MakeDir($this->query);
        $mkDir->setKey($this->previousKey())->setSequence($this->generateSequence())->setDirPath($path)->compile();
        /** @var Fsp\Answer\Protection|Fsp\Answer\Error $answer */
        $answer = $this->sendQuery($this->query);
        if ($answer instanceof Fsp\Answer\Error) {
            throw $answer->getError();
        }
        if (!$answer instanceof Fsp\Answer\Protection) {
            throw new RemoteRequest\RequestException('Got something bad with mkdir. Class ' . get_class($answer));
        }
        $this->parseAnswer($answer);
        // TODO: send protection data - set from $mode
        return true;
    }

    /**
     * @param string $path_from
     * @param string $path_to
     * @return bool
     * @throws RemoteRequest\RequestException
     */
    public function rename(string $path_from, string $path_to): bool
    {
        $rename = new Fsp\Query\Rename($this->query);
        $rename->setKey($this->previousKey())->setSequence($this->generateSequence())->setFilePath($path_from)->setNewPath($path_to)->compile();
        /** @var Fsp\Answer\Nothing|Fsp\Answer\Error $answer */
        $answer = $this->sendQuery($this->query);
        if ($answer instanceof Fsp\Answer\Error) {
            throw $answer->getError();
        }
        if (!$answer instanceof Fsp\Answer\Nothing) {
            throw new RemoteRequest\RequestException('Got something bad with rename. Class ' . get_class($answer));
        }
        $this->parseAnswer($answer);
        return true;
    }

    /**
     * @param string $path
     * @param int $options
     * @return bool
     * @throws RemoteRequest\RequestException
     */
    public function rmdir(string $path, int $options): bool
    {
        $delDir = new Fsp\Query\DelDir($this->query);
        $delDir->setKey($this->previousKey())->setSequence($this->generateSequence())->setDirPath($path)->compile();
        /** @var Fsp\Answer\Nothing|Fsp\Answer\Error $answer */
        $answer = $this->sendQuery($this->query);
        if ($answer instanceof Fsp\Answer\Error) {
            throw $answer->getError();
        }
        if (!$answer instanceof Fsp\Answer\Nothing) {
            throw new RemoteRequest\RequestException('Got something bad with rmdir. Class ' . get_class($answer));
        }
        $this->parseAnswer($answer);
        return true;
    }

    /**
     * @param int $cast_as
     * @return resource
     */
    public function stream_cast(int $cast_as)
    {
    }

    public function stream_close(): void
    {
    }

    public function stream_eof(): bool
    {
        return $this->position >= strlen($GLOBALS[$this->varname]);
    }

    public function stream_flush(): bool
    {
    }

    public function stream_lock(int $operation): bool
    {
    }

    public function stream_metadata(string $path, int $option, $var): bool
    {
        if($option == STREAM_META_TOUCH) {
            $url = parse_url($path);
            $varname = $url["host"];
            if(!isset($GLOBALS[$varname])) {
                $GLOBALS[$varname] = '';
            }
            return true;
        }
        return false;
    }

    function stream_open(string $path, string $mode, int $options, string &$opened_path): bool
    {
        $url = parse_url($path);
        $this->varname = $url["host"];
        $this->position = 0;

        return true;
    }

    public function stream_read(int $count): string
    {
        $ret = substr($GLOBALS[$this->varname], $this->position, $count);
        $this->position += strlen($ret);
        return $ret;
    }

    public function stream_seek(int $offset, int $whence = SEEK_SET): bool
    {
        switch ($whence) {
            case SEEK_SET:
                if ($offset < strlen($GLOBALS[$this->varname]) && $offset >= 0) {
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
                if (strlen($GLOBALS[$this->varname]) + $offset >= 0) {
                    $this->position = strlen($GLOBALS[$this->varname]) + $offset;
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
    }

    public function stream_stat(): array
    {
    }

    public function stream_tell(): int
    {
        return $this->position;
    }

    public function stream_truncate(int $new_size): bool
    {
    }

    public function stream_write(string $data): int
    {
        $left = substr($GLOBALS[$this->varname], 0, $this->position);
        $right = substr($GLOBALS[$this->varname], $this->position + strlen($data));
        $GLOBALS[$this->varname] = $left . $data . $right;
        $this->position += strlen($data);
        return strlen($data);
    }

    /**
     * @param string $path
     * @return bool
     * @throws RemoteRequest\RequestException
     */
    public function unlink(string $path): bool
    {
        $delFile = new Fsp\Query\DelFile($this->query);
        $delFile->setKey($this->previousKey())->setSequence($this->generateSequence())->setFilePath($path)->compile();
        /** @var Fsp\Answer\Nothing|Fsp\Answer\Error $answer */
        $answer = $this->sendQuery($this->query);
        if ($answer instanceof Fsp\Answer\Error) {
            throw $answer->getError();
        }
        if (!$answer instanceof Fsp\Answer\Nothing) {
            throw new RemoteRequest\RequestException('Got something bad with unlink. Class ' . get_class($answer));
        }
        $this->parseAnswer($answer);
        return true;
    }

    public function url_stat(string $path, int $flags): array
    {
    }

    /**
     * @throws RemoteRequest\RequestException
     */
    protected function sendBye(): void
    {
        $bye = new Fsp\Query\Bye($this->query);
        $bye->setKey($this->previousKey())->setSequence($this->generateSequence())->compile();
        /** @var Fsp\Answer\Nothing|Fsp\Answer\Error $answer */
        $answer = $this->sendQuery($this->query);
        if ($answer instanceof Fsp\Answer\Error) {
            throw $answer->getError();
        }
        if (!$answer instanceof Fsp\Answer\Nothing) {
            throw new RemoteRequest\RequestException('Got something bad with close. Class ' . get_class($answer));
        }
        $this->key = null;
        $this->sequence = [];
    }

    /**
     * @param Fsp\Query $query
     * @return Fsp\Answer\AAnswer
     * @throws RemoteRequest\RequestException
     */
    protected function sendQuery(Fsp\Query $query): Fsp\Answer\AAnswer
    {
        return Fsp\Answer\AnswerFactory::getObject(
            $this->answer->setResponse(
                $this->processor->setProtocolWrapper($this->wrapper)->setData($query)->getResponse()
            )->process()
        );
    }

    protected function previousKey(): int
    {
        return isset($this->key) ? $this->key : rand(0, 255);
    }

    protected function generateSequence(): int
    {
        $seqKey = rand(0, 255);
        $this->sequence[$seqKey] = microtime();
        return $seqKey;
    }

    protected function parseAnswer(Fsp\Answer\AAnswer $answer): void
    {
        $raw = $answer->getDataClass();
        $this->key = $raw->getKey();
        $this->sequence[$raw->getSequence()] = microtime() - $this->sequence[$raw->getSequence()];
    }
}


if (in_array("fsp", stream_get_wrappers())) {
    stream_wrapper_unregister("fsp");
}
stream_wrapper_register("fsp", "\RemoteRequest\Wrappers\FspWrapper");
