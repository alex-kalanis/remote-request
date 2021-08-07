<?php

namespace RemoteRequest\Protocols\Fsp;


use RemoteRequest\Protocols;


/**
 * Class Query
 * @package RemoteRequest\Protocols\Fsp
 * Simple FSP query to remote source - create packet
 */
class Query extends Protocols\Dummy\Query
{
    use Traits\THeader;
    use Traits\TChecksum;

    public $maxLength = Protocols\Fsp::MAX_PACKET_SIZE;
    protected $headCommand = 0;
    protected $headServerKey = 0;
    protected $headSequence = 0;
    protected $headFilePosition = 0;
    protected $contentData = '';
    protected $contentExtraData = '';

    private $preparedPacket = '';

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

    public function setFilePosition(int $filePosition)
    {
        $this->headFilePosition = $filePosition;
        return $this;
    }

    public function setContent(string $data): self
    {
        $this->contentData = $data;
        return $this;
    }

    public function setExtraData(string $extraData): self
    {
        $this->contentExtraData = $extraData;
        return $this;
    }

    public function getPacket(): string
    {
        $this->preparedPacket = $this->renderRequestHeader() . $this->getContent() . $this->getExtraData();
        $this->preparedPacket[1] = chr($this->computeCheckSum());
        return $this->preparedPacket;
    }

    public function getChecksumPacket(): string
    {
        return $this->preparedPacket;
    }

    public function getInitialSumChunk(): int
    {
        return strlen($this->preparedPacket);
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

    protected function getDataLength(): int
    {
        return strlen($this->getContent());
    }

    protected function getFilePosition(): int
    {
        return (int)$this->headFilePosition;
    }

    protected function getContent(): string
    {
        return (string)$this->contentData;
    }

    protected function getExtraData(): string
    {
        return (string)$this->contentExtraData;
    }
}
