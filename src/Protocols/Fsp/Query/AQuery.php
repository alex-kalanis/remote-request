<?php

namespace RemoteRequest\Protocols\Fsp\Query;


use RemoteRequest\Protocols\Fsp;


/**
 * Class AQuery
 * @package RemoteRequest\Protocols\Fsp\Query
 * Abstract processor - common calls across the answer
 */
abstract class AQuery
{
    protected $query = null;
    protected $serverKey = 0;
    protected $localSequence = 0;

    public function __construct(Fsp\Query $query)
    {
        $this->query = $query;
    }

    public function setKey(int $key = 0)
    {
        $this->serverKey = $key;
        return $this;
    }

    public function setSequence(int $sequenceNumber = 0)
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
     * @see \RemoteRequest\Protocols\Fsp
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
