<?php

namespace RemoteRequest\Protocols\Fsp\Traits;

/**
 * Process checksums
 */
trait TChecksum
{
    protected function computeCheckSum(): int
    {
        $data = $this->renderRequestHeader(0) . $this->getContent() . $this->getExtraData();
        $sum = array_reduce(str_split($data), [$this, 'sumBytes'], $this->getInitialSumChunk($data));
        return ($sum + ($sum >> 8)) & 0xff;
    }

    abstract protected function renderRequestHeader(int $checksum): string;

    abstract protected function getInitialSumChunk(string $data): int;

    protected function sumBytes(int $sum, string $char): int
    {
        return $sum + ord($char);
    }

    abstract protected function getContent(): string;

    abstract protected function getExtraData(): string;
}