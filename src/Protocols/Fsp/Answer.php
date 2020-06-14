<?php

namespace RemoteRequest\Protocols\Fsp;

use RemoteRequest\Protocols;
use RemoteRequest\RequestException;

/**
 * Process server's answer - FSP packet
 */
class Answer extends Protocols\Dummy\Answer
{
    use Traits\THeader;
    use Traits\TChecksum;

    protected $headChecksum = 0;
    protected $headCommand = 0;
    protected $headServerKey = 0;
    protected $headSequence = 0;
    protected $headDataLength = 0;
    protected $headFilePosition = 0;
    protected $header = '';
    protected $content = '';
    protected $extra = '';

    public $canDump = false; // for dump info about checksums

    /**
     * @return Answer
     * @throws RequestException
     */
    public function process(): Answer
    {
        $this->getHeader();
        $this->processHeader();
        $this->processContent();
        $this->checkResponse();
        return $this;
    }

    protected function getHeader(): void
    {
        $this->header = substr($this->body, 0, Protocols\Fsp::HEADER_SIZE);
    }

    protected function processHeader(): void
    {
        $this->headCommand = $this->headerParse($this->header, 0, 1);
        $this->headChecksum = $this->headerParse($this->header, 1, 1);
        $this->headServerKey = $this->headerParse($this->header, 2, 2);
        $this->headSequence = $this->headerParse($this->header, 4, 2);
        $this->headDataLength = $this->headerParse($this->header, 6, 2);
        $this->headFilePosition = $this->headerParse($this->header, 8, 4);
    }

    protected function processContent(): void
    {
        $content = substr($this->body, Protocols\Fsp::HEADER_SIZE);
        $this->content = substr($content, 0, $this->getDataLength());
        $this->extra = substr($content, $this->getDataLength());
    }

    /**
     * Generate server checksum from data and compare them
     * @throws RequestException
     */
    protected function checkResponse(): void
    {
        // @codeCoverageIgnoreStart
        // necessary dumper - who can math checksums from their head?
        if ($this->canDump) {
            var_dump(['chksums', 'calc' => $this->computeCheckSum(), 'got' => $this->headChecksum ]);
        }
        // @codeCoverageIgnoreEnd
        if ($this->computeCheckSum() != $this->headChecksum ) {
            throw new RequestException('Invalid checksum');
        }
    }

    public function sumChunk(int $sum, string $data): int
    {
        # FIXME: this checksum computation is likely slow...
        return array_reduce(str_split($data), [$this, 'sumBytes'], $sum);
    }

    public function getCommand(): string
    {
        return (string)$this->headCommand;
    }

    public function getKey(): int
    {
        return (int)$this->headServerKey;
    }

    public function getSequence(): int
    {
        return (int)$this->headSequence;
    }

    public function getDataLength(): int
    {
        return (string)$this->headDataLength;
    }

    public function getFilePosition(): int
    {
        return (string)$this->headFilePosition;
    }

    public function getContent(): string
    {
        return (string)$this->content;
    }

    public function getExtraData(): string
    {
        return (string)$this->extra;
    }
}