<?php

namespace kalanis\RemoteRequest\Protocols\Fsp\Answer\GetDir;


use kalanis\RemoteRequest\Protocols\Fsp;
use SplFileInfo;


/**
 * Class FileInfo
 * @package kalanis\RemoteRequest\Protocols\Fsp\Answer\GetDir
 * Process Get file info
 * @link https://www.php.net/manual/en/class.splfileobject.php
 */
class FileInfo extends SplFileInfo
{
    /** @var array<int, string> */
    protected static array $types = [
        Fsp::RDTYPE_DIR => 'dir',
        Fsp::RDTYPE_FILE => 'file',
        Fsp::RDTYPE_LINK => 'link',
        Fsp::RDTYPE_SKIP => 'skip',
        Fsp::RDTYPE_END => 'end',
    ];

    protected string $path = '';
    protected string $file_name = '';
    protected string $link_name = '';
    protected int $size = 0;
    protected int $time = 0;
    protected int $type = 0;

    public function setData(string $data, string $path = ''): self
    {
        $this->path = $path;
        $this->time = Fsp\Strings::mb_ord(substr($data, 0, 4));
        $this->size = Fsp\Strings::mb_ord(substr($data, 4, 4));
        $this->type = Fsp\Strings::mb_ord($data[8]);
        if (Fsp::RDTYPE_END != $this->type && Fsp::RDTYPE_SKIP != $this->type) {
            $this->file_name = rtrim(substr($data, 9), "\t\n\r\0\x0B"); // spaces must stay
            if ($this->isLink()) {
                $this->parseLinkName();
            }
        }
        return $this;
    }

    protected function parseLinkName(): void
    {
        $nlpos = strpos($this->file_name, "\n");
        $this->link_name = $nlpos ? substr($this->file_name, $nlpos + 1) : '' ;
        $this->file_name = $this->link_name && $nlpos ? substr($this->file_name, 0, $nlpos) : $this->file_name ;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;
        return $this;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getFilename(): string
    {
        return $this->file_name;
    }

    public function getExtension(): string
    {
        $dot = strrpos($this->file_name, '.');
        return $dot ? substr($this->file_name, $dot + 1) : '';
    }

    public function getPathname(): string
    {
        return $this->path . DIRECTORY_SEPARATOR . $this->file_name;
    }

    public function getPerms(): int
    {
        // need server info
        return 0666;
    }

    public function getInode(): int
    {
        return 0;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getOwner(): int
    {
        return 0;
    }

    public function getGroup(): int
    {
        return 0;
    }

    public function getATime(): int
    {
        return $this->time;
    }

    public function getMTime(): int
    {
        return $this->time;
    }

    public function getCTime(): int
    {
        return $this->time;
    }

    public function getType(): string
    {
        return static::$types[$this->type];
    }

    public function getOrigType(): int
    {
        return $this->type;
    }

    public function isWritable(): bool
    {
        /// need server info
        return true;
    }

    public function isReadable(): bool
    {
        /// need server info
        return true;
    }

    public function isExecutable(): bool
    {
        return $this->isDir();
    }

    public function isFile(): bool
    {
        return Fsp::RDTYPE_FILE == $this->type;
    }

    public function isDir(): bool
    {
        return Fsp::RDTYPE_DIR == $this->type;
    }

    public function isLink(): bool
    {
        return Fsp::RDTYPE_LINK == $this->type;
    }

    public function getLinkTarget(): string
    {
        return $this->isLink() ? $this->link_name : '' ;
    }

    #[\ReturnTypeWillChange]
    public function getRealPath()
    {
        return $this->path . DIRECTORY_SEPARATOR . $this->file_name;
    }
}
