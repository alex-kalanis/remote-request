<?php

namespace RemoteRequest\Wrappers\Fsp;

use RemoteRequest;
use RemoteRequest\Protocols\Fsp as Protocol;

/**
 * Wrapper to plug FSP info into PHP - files part
 */
class File extends AOperations
{
    /* Properties */
    /** @var resource */
    protected $context;
    protected $position;
    protected $varname;

    protected $path = '';
    protected $seek = 0;

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
        return false;
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
        $delFile = new Protocol\Query\DelFile($this->runner->getQuery());
        $delFile->setFilePath($this->parsePath($path));
        $answer = $this->runner->setActionQuery($delFile)->process();
        if (!$answer instanceof Protocol\Answer\Nothing) {
            throw new RemoteRequest\RequestException('Got something bad with unlink. Class ' . get_class($answer));
        }
        return true;
    }
}
