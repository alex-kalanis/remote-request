<?php

namespace RemoteRequest\Protocols\Fsp\Traits;

/**
 * Process checksums
 */
trait TChecksum
{
    protected function computeCheckSum(): int
    {
        $sum = array_reduce([
            $this->renderRequestHeader(0),
            $this->getContent(),
            $this->getExtraData(),
        ], [$this, 'sumChunk'], 0);
        return ($sum + ($sum >> 8)) & 0xff;
    }

    abstract protected function renderRequestHeader(int $checksum): string;

    abstract public function sumChunk(int $sum, string $data): int;

    public function sumBytes(int $sum, string $char): int
    {
        return $sum + ord($char);
    }

    abstract protected function getContent(): string;

    abstract protected function getExtraData(): string;
}