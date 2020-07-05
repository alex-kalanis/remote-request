<?php

namespace RemoteRequest\Wrappers;

use RemoteRequest;
use RemoteRequest\Protocols\Fsp as Protocol;

/**
 * Wrapper to plug FSP info into PHP
 * - for direct call fsp via php - the connection itself is in libraries outside
 * @link https://www.php.net/manual/en/class.streamwrapper.php
 * @link https://www.php.net/manual/en/stream.streamwrapper.example-1.php
 *
 * Usage:
 * - In initialization:
RemoteRequest\Wrappers\Fsp::register();
 * - somewhere in code:
file_get_contents('fsp://user:pass@server:12345/dir/file');
 */
class Fsp
{
    /* Properties */
    /** @var resource */
    protected $context;
    protected $position;
    protected $varname;
    protected $runner = null;
    protected $dir = null;
    protected $file = null;

    protected $path = '';

    public static function register()
    {
        if (in_array("fsp", stream_get_wrappers())) {
            stream_wrapper_unregister("fsp");
        }
        stream_wrapper_register("fsp", "\RemoteRequest\Wrappers\Fsp");
    }

    /* Methods */
    public function __construct()
    {
        $this->runner = new Protocol\Runner();
        $this->dir = new Fsp\Dir($this->runner);
        $this->file = new Fsp\File($this->runner);
    }

    public function __destruct()
    {
        $this->runner->__destruct();
    }

    public function dir_closedir(): bool
    {
        try {
            return $this->dir->close();
        } catch (RemoteRequest\RequestException $ex) {
            trigger_error($ex->getMessage(), E_USER_ERROR);
            return false;
        }
    }

    public function dir_opendir(string $path, int $options): bool
    {
        try {
            return $this->dir->open($path, $options);
        } catch (RemoteRequest\RequestException $ex) {
            trigger_error($ex->getMessage(), E_USER_ERROR);
            return false;
        }
    }

    /**
     * @return string|false
     */
    public function dir_readdir()
    {
        try {
            return $this->dir->read();
        } catch (RemoteRequest\RequestException $ex) {
            trigger_error($ex->getMessage(), E_USER_ERROR);
            return false;
        }
    }

    public function dir_rewinddir(): bool
    {
        try {
            return $this->dir->rewind();
        } catch (RemoteRequest\RequestException $ex) {
            trigger_error($ex->getMessage(), E_USER_ERROR);
            return false;
        }
    }

    /**
     * @param string $path
     * @param int $mode
     * @param int $options
     * @return bool
     */
    public function mkdir(string $path, int $mode, int $options): bool
    {
        try {
            return $this->dir->make($path, $mode, $options);
        } catch (RemoteRequest\RequestException $ex) {
            trigger_error($ex->getMessage(), E_USER_ERROR);
            return false;
        }
    }

    /**
     * @param string $path_from
     * @param string $path_to
     * @return bool
     */
    public function rename(string $path_from, string $path_to): bool
    {
        try {
            return $this->dir->rename($path_from, $path_to);
        } catch (RemoteRequest\RequestException $ex) {
            trigger_error($ex->getMessage(), E_USER_ERROR);
            return false;
        }
    }

    /**
     * @param string $path
     * @param int $options
     * @return bool
     */
    public function rmdir(string $path, int $options): bool
    {
        try {
            return $this->dir->remove($path, $options);
        } catch (RemoteRequest\RequestException $ex) {
            trigger_error($ex->getMessage(), E_USER_ERROR);
            return false;
        }
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
     */
    public function unlink(string $path): bool
    {
        try {
            return $this->file->unlink($path);
        } catch (RemoteRequest\RequestException $ex) {
            trigger_error($ex->getMessage(), E_USER_ERROR);
            return false;
        }
    }

    public function url_stat(string $path, int $flags): array
    {
        try {
            return $this->dir->stats($path, $flags);
        } catch (RemoteRequest\RequestException $ex) {
            if ($flags & ~STREAM_URL_STAT_QUIET) {
                trigger_error($ex->getMessage(), E_USER_ERROR);
            }
            return [
                0 => 0,
                1 => 0,
                2 => 0,
                3 => 0,
                4 => 0,
                5 => 0,
                6 => 0,
                7 => 0,
                8 => 0,
                9 => 0,
                10 => 0,
                11 => -1,
                12 => -1,
            ];
        }
    }
}
