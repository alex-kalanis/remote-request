<?php

namespace kalanis\RemoteRequest\Protocols\Fsp;


use kalanis\RemoteRequest\Protocols;


/**
 * Class Query
 * @package RemoteRequest\Protocols\Fsp
 * Simple FSP query to remote source - create packet
 * This is the "transport box"
 * @todo: throw compute checksum and packet building outside this class; that's logic, not pure data
 *        then getData() call can have that logic
 *     - X - -> re-define where are the data storages (local - this class; and remote - the packet into the run)
 *           - then it's simple case of many adapters and that calculating calls should be there
 *           - Adapters in HTTP are descendants of Dummy\Query and Dummy\Answer
 */
class Query extends Protocols\Dummy\Query
{
    use Traits\THeader;
    use Traits\TChecksum;

    public ?int $maxLength = Protocols\Fsp::MAX_PACKET_SIZE;
    protected int $headCommand = 0;
    protected int $headServerKey = 0;
    protected int $headSequence = 0;
    protected int $headFilePosition = 0;
    protected string $contentData = '';
    protected string $contentExtraData = '';

    private string $preparedPacket = '';

    public function setKey(int $key = 0): self
    {
        $this->headServerKey = $key;
        return $this;
    }

    public function setSequence(int $sequenceNumber = 0): self
    {
        $this->headSequence = $sequenceNumber;
        return $this;
    }

    public function setCommand(int $command): self
    {
        $this->headCommand = $command;
        return $this;
    }

    public function setFilePosition(int $filePosition): self
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
        return strval($this->preparedPacket);
    }

    public function getInitialSumChunk(): int
    {
        return strlen($this->preparedPacket);
    }

    protected function getCommand(): int
    {
        return intval($this->headCommand);
    }

    protected function getKey(): int
    {
        return intval($this->headServerKey);
    }

    protected function getSequence(): int
    {
        return intval($this->headSequence);
    }

    protected function getDataLength(): int
    {
        return strlen($this->getContent());
    }

    protected function getFilePosition(): int
    {
        return intval($this->headFilePosition);
    }

    protected function getContent(): string
    {
        return strval($this->contentData);
    }

    protected function getExtraData(): string
    {
        return strval($this->contentExtraData);
    }
}
