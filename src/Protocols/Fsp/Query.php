<?php

namespace RemoteRequest\Protocols\Fsp;

use RemoteRequest\Protocols;

/**
 * Simple FSP query to remote source
 */
class Query extends Protocols\Dummy\Query
{
    use THeader;
    use TChecksum;

    protected $headCommand = 0;
    protected $headServerKey = 0;
    protected $headSequence = 0;
    protected $headFilePosition = 0;
    public $contentXtraData = '';

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

    /**
     * @return $this
     * @codeCoverageIgnore
     */
    public function wantVersion()
    {
        $this->headCommand = Protocols\Fsp::CC_VERSION;
        return $this;
    }

    /**
     * @return $this
     * @codeCoverageIgnore
     */
    public function wantError()
    {
        $this->headCommand = Protocols\Fsp::CC_ERR;
        return $this;
    }

    /**
     * @return $this
     * @codeCoverageIgnore
     */
    public function wantFile()
    {
        $this->headCommand = Protocols\Fsp::CC_GET_FILE;
        return $this;
    }

    public function wantDir()
    {
        $this->headCommand = Protocols\Fsp::CC_GET_DIR;
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
        return (string)$this->contentXtraData;
    }
}