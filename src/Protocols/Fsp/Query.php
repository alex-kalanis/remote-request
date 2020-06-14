<?php

namespace RemoteRequest\Protocols\Fsp;

use RemoteRequest\Protocols;

/**
 * Simple FSP query to remote source - create packet
 */
class Query extends Protocols\Dummy\Query
{
    use Traits\THeader;
    use Traits\TChecksum;

    protected $headCommand = 0;
    protected $headServerKey = 0;
    protected $headSequence = 0;
    protected $headFilePosition = 0;
    protected $contentExtraData = '';

    public function setKey(int $key = 0)
    {
        $this->headServerKey = $key;
        return $this;
    }

    public function setSequence(int $sequenceNumber = 0)
    {
        $this->headSequence = $sequenceNumber;
        return $this;
    }

    public function setCommand(int $command)
    {
        $this->headCommand = $command;
        return $this;
    }

    public function setData(string $data): self
    {
        $this->body = $data;
        return $this;
    }

    public function setExtraData(string $extraData): self
    {
        $this->contentExtraData = $extraData;
        return $this;
    }

    public function getData(): string
    {
        return sprintf("%s%s%s", $this->renderRequestHeader($this->computeCheckSum()), $this->getContent(), $this->getExtraData());
    }

    public function sumChunk(int $sum, string $data): int
    {
        # FIXME: this checksum computation is likely slow...
        return array_reduce(str_split($data), [$this, 'sumBytes'], $sum + strlen($data));
    }

    protected function getCommand(): int
    {
        return (int)$this->headCommand;
    }

    protected function getKey(): int
    {
        return (int)$this->headServerKey;
    }

    protected function getSequence(): int
    {
        return (int)$this->headSequence;
    }

    protected function getFilePosition(): int
    {
        return (int)$this->headFilePosition;
    }

    protected function getContent(): string
    {
        return (string)$this->body;
    }

    protected function getExtraData(): string
    {
        return (string)$this->contentExtraData;
    }
}