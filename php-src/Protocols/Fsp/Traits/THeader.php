<?php

namespace kalanis\RemoteRequest\Protocols\Fsp\Traits;


use kalanis\RemoteRequest\Protocols\Fsp;


/**
 * Trait THeader
 * @package kalanis\RemoteRequest\Protocols\Fsp\Traits
 * Process header
 */
trait THeader
{
    protected function renderRequestHeader(): string
    {
        return sprintf('%s%s%s%s%s%s',
            Fsp\Strings::filler($this->getCommand(), 1),
            Fsp\Strings::filler(0, 1),
            Fsp\Strings::filler($this->getKey(), 2),
            Fsp\Strings::filler($this->getSequence(), 2),
            Fsp\Strings::filler($this->getDataLength(), 2),
            Fsp\Strings::filler($this->getFilePosition(), 4)
        );
    }

    abstract protected function getCommand(): int;

    abstract protected function getKey(): int;

    abstract protected function getSequence(): int;

    abstract protected function getDataLength(): int;

    abstract protected function getFilePosition(): int;

    abstract protected function getContent(): string;
}
