<?php

namespace RemoteRequest\Protocols\Fsp\Traits;

use RemoteRequest\Protocols\Fsp;

/**
 * Process header
 */
trait THeader
{
    protected function renderRequestHeader(int $checksum): string
    {
        return sprintf('%s%s%s%s%s%s',
            $this->headerFill($this->getCommand(), 1),
            $this->headerFill($checksum, 1),
            $this->headerFill($this->getKey(), 2),
            $this->headerFill($this->getSequence(), 2),
            $this->headerFill(strlen($this->getContent()), 2),
            $this->headerFill($this->getFilePosition(), 4)
        );
    }

    protected function headerFill(int $input, int $length): string
    {
        return str_pad(
            substr(Fsp\Strings::mb_chr($input), 0, $length),
            $length,
            chr(0),
            STR_PAD_LEFT);
    }

    protected function headerParse(string $header, int $start, int $length): int
    {
        return Fsp\Strings::mb_ord(substr($header, $start, $length));
    }

    abstract protected function getCommand(): int;

    abstract protected function getKey(): int;

    abstract protected function getSequence(): int;

    abstract protected function getFilePosition(): int;

    abstract protected function getContent(): string;
}