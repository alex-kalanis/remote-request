<?php

namespace kalanis\RemoteRequest\Wrappers;


use kalanis\RemoteRequest\RequestException;
use kalanis\RemoteRequest\Translations;


/**
 * Class Fsp
 * @package kalanis\RemoteRequest\Wrappers
 * Wrapper to plug FSP info into PHP
 * - for direct call fsp via php - the connection itself is in libraries outside
 * @link https://www.php.net/manual/en/class.streamwrapper.php
 * @link https://www.php.net/manual/en/stream.streamwrapper.example-1.php
 *
 * Usage:
 * - In initialization:
kalanis\RemoteRequest\Wrappers\Fsp::register();
 * - somewhere in code:
file_get_contents('fsp://user:pass@server:12345/dir/file');
 */
class Fsp
{
    /** @var resource */
    public $context;

    /** @var Fsp\Runner */
    protected $runner = null;
    /** @var Fsp\Dir */
    protected $dir = null;
    /** @var Fsp\File */
    protected $file = null;
    /** @var bool */
    protected $showErrors = true;

    public static function register(): void
    {
        if (in_array("fsp", stream_get_wrappers())) {
            stream_wrapper_unregister("fsp");
        }
        stream_wrapper_register("fsp", "\RemoteRequest\Wrappers\Fsp");
    }

    public function __construct()
    {
        $lang = new Translations();
        $this->runner = new Fsp\Runner($lang);
        $this->dir = new Fsp\Dir($lang, $this->runner);
        $this->file = new Fsp\File($lang, $this->runner);
    }

    public function __destruct()
    {
        try {
            $this->runner->__destruct();
        } catch (RequestException $ex) {
            $this->errorReport($ex);
        }
    }

    public function dir_closedir(): bool
    {
        try {
            return $this->dir->close();
        } catch (RequestException $ex) {
            $this->errorReport($ex);
            return false;
        }
    }

    public function dir_opendir(string $path, int $options): bool
    {
        try {
            return $this->dir->open($path, $options);
        } catch (RequestException $ex) {
            $this->errorReport($ex);
            return false;
        }
    }

    /**
     * @return string|bool
     */
    public function dir_readdir()
    {
        try {
            return $this->dir->read();
        } catch (RequestException $ex) {
            $this->errorReport($ex);
            return false;
        }
    }

    public function dir_rewinddir(): bool
    {
        try {
            return $this->dir->rewind();
        } catch (RequestException $ex) {
            $this->errorReport($ex);
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
        } catch (RequestException $ex) {
            $this->errorReport($ex);
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
        } catch (RequestException $ex) {
            $this->errorReport($ex);
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
        } catch (RequestException $ex) {
            $this->errorReport($ex);
            return false;
        }
    }

    /**
     * @param int $cast_as
     * @return resource|bool
     */
    public function stream_cast(int $cast_as)
    {
        try {
            return $this->file->stream_cast($cast_as);
        } catch (RequestException $ex) {
            $this->errorReport($ex);
            return false;
        }
    }

    public function stream_close(): void
    {
        try {
            $this->file->stream_close();
        } catch (RequestException $ex) {
            $this->errorReport($ex);
        }
    }

    public function stream_eof(): bool
    {
        try {
            return $this->file->stream_eof();
        } catch (RequestException $ex) {
            $this->errorReport($ex);
            return true;
        }
    }

    public function stream_flush(): bool
    {
        try {
            return $this->file->stream_flush();
        } catch (RequestException $ex) {
            $this->errorReport($ex);
            return false;
        }
    }

    public function stream_lock(int $operation): bool
    {
        try {
            return $this->file->stream_lock($operation);
        } catch (RequestException $ex) {
            $this->errorReport($ex);
            return false;
        }
    }

    /**
     * @param string $path
     * @param int $option
     * @param mixed $var
     * @return bool
     */
    public function stream_metadata(string $path, int $option, $var): bool
    {
        try {
            return $this->file->stream_metadata($path, $option, $var);
        } catch (RequestException $ex) {
            $this->errorReport($ex);
            return false;
        }
    }

    public function stream_open(string $path, string $mode, int $options, /** @scrutinizer ignore-unused */ string &$opened_path): bool
    {
        try {
            $this->canReport($options);
            return $this->file->stream_open($this->dir, $path, $mode);
        } catch (RequestException $ex) {
            $this->errorReport($ex);
            return false;
        }
    }

    public function stream_read(int $count): string
    {
        try {
            return $this->file->stream_read($count);
        } catch (RequestException $ex) {
            $this->errorReport($ex);
            return '';
        }
    }

    public function stream_seek(int $offset, int $whence = SEEK_SET): bool
    {
        try {
            return $this->file->stream_seek($offset, $whence);
        } catch (RequestException $ex) {
            $this->errorReport($ex);
            return false;
        }
    }

    public function stream_set_option(int $option, int $arg1, int $arg2): bool
    {
        try {
            return $this->file->stream_set_option($option, $arg1, $arg2);
        } catch (RequestException $ex) {
            $this->errorReport($ex);
            return false;
        }
    }

    /**
     * @return array<int, int>
     */
    public function stream_stat(): array
    {
        try {
            return $this->file->stream_stat($this->dir);
        } catch (RequestException $ex) {
            $this->errorReport($ex);
            return [];
        }
    }

    public function stream_tell(): int
    {
        try {
            return $this->file->stream_tell();
        } catch (RequestException $ex) {
            $this->errorReport($ex);
            return -1;
        }
    }

    public function stream_truncate(int $new_size): bool
    {
        try {
            return $this->file->stream_truncate($new_size);
        } catch (RequestException $ex) {
            $this->errorReport($ex);
            return false;
        }
    }

    public function stream_write(string $data): int
    {
        try {
            return $this->file->stream_write($data);
        } catch (RequestException $ex) {
            $this->errorReport($ex);
            return 0;
        }
    }

    /**
     * @param string $path
     * @return bool
     */
    public function unlink(string $path): bool
    {
        try {
            return $this->file->unlink($path);
        } catch (RequestException $ex) {
            $this->errorReport($ex);
            return false;
        }
    }

    protected function canReport(int $opts): void
    {
        $this->showErrors = boolval($opts & STREAM_REPORT_ERRORS);
    }

    /**
     * @param RequestException $ex
     */
    protected function errorReport(RequestException $ex): void
    {
        if ($this->showErrors) {
            trigger_error($ex->getMessage(), E_USER_ERROR);
        }
    }

    /**
     * @param string $path
     * @param int $flags
     * @return array<int, int>
     */
    public function url_stat(string $path, int $flags): array
    {
        try {
            return $this->dir->stats($path, $flags);
        } catch (RequestException $ex) {
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
