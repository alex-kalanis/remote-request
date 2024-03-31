<?php

namespace kalanis\RemoteRequest\Protocols\Fsp\Query;


use kalanis\RemoteRequest\Protocols\Fsp;


/**
 * Class AQuery
 * @package kalanis\RemoteRequest\Protocols\Fsp\Query
 * Abstract processor - common calls across the answer
 */
abstract class AQuery
{
    protected Fsp\Query $query;
    protected int $serverKey = 0;
    protected int $localSequence = 0;

    public function __construct(Fsp\Query $query)
    {
        $this->query = $query;
    }

    public function setKey(int $key = 0): self
    {
        $this->serverKey = $key;
        return $this;
    }

    public function setSequence(int $sequenceNumber = 0): self
    {
        $this->localSequence = $sequenceNumber;
        return $this;
    }

    public function compile(): string
    {
        return $this->query->body = $this->query
            ->setCommand($this->getCommand())
            ->setKey($this->serverKey)
            ->setSequence($this->localSequence)
            ->setFilePosition($this->getFilePosition())
            ->setContent($this->getData())
            ->setExtraData($this->getExtraData())
            ->getPacket()
        ;
    }

    /**
     * Commands as defined in \RemoteRequest\Protocols\Fsp::CC_*
     * @return int
     * @see \kalanis\RemoteRequest\Protocols\Fsp
     */
    abstract protected function getCommand(): int;

    protected function getFilePosition(): int
    {
        return 0;
    }

    protected function getData(): string
    {
        return '';
    }

    protected function getExtraData(): string
    {
        return '';
    }
}
