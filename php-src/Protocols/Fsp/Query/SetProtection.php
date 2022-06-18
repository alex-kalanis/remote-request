<?php

namespace kalanis\RemoteRequest\Protocols\Fsp\Query;


use kalanis\RemoteRequest\Protocols\Fsp;


/**
 * Class SetProtection
 * @package kalanis\RemoteRequest\Protocols\Fsp\Query
 * Set dir protection details
 */
class SetProtection extends AQuery
{
    const CAN_CREATE_FILE = 'c';
    const CAN_DELETE_FILE = 'd';
    const CAN_GET_FILE = 'g';
    const CAN_PRESERVE_FILE = 'p'; // backward compatibility
    const CAN_CREATE_DIR = 'c';
    const CAN_LIST_DIR = 'd';
    const CAN_RENAME_FILE = 'g';

    const ALLOW = '+';
    const DISCARD = '-';

    /** @var string */
    protected $dirPath = '';
    /** @var string|null */
    protected $operation = null;
    /** @var bool|null */
    protected $allow = null;

    protected function getCommand(): int
    {
        return Fsp::CC_SET_PRO;
    }

    public function setDirPath(string $filePath): self
    {
        $this->dirPath = $filePath;
        return $this;
    }

    public function setOperation(string $operation): self
    {
        if (in_array($operation, [
            static::CAN_CREATE_FILE,
            static::CAN_DELETE_FILE,
            static::CAN_GET_FILE,
            static::CAN_PRESERVE_FILE,
            static::CAN_CREATE_DIR,
            static::CAN_LIST_DIR,
            static::CAN_RENAME_FILE,
        ])) {
            $this->operation = $operation;
        }
        return $this;
    }

    public function allowOperation(bool $allow): self
    {
        $this->allow = $allow;
        return $this;
    }

    protected function getFilePosition(): int
    {
        return strlen($this->getExtraData());
    }

    protected function getData(): string
    {
        return $this->dirPath . chr(0);
    }

    protected function getExtraData(): string
    {
        if (static::CAN_PRESERVE_FILE == $this->operation) {
            $this->operation = static::CAN_RENAME_FILE;
            $this->allow = !$this->allow;
        }
        return ($this->allow ? static::ALLOW : static::DISCARD) . $this->operation ;
    }
}
