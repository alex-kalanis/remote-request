<?php

namespace RemoteRequest\Protocols\Fsp;


use RemoteRequest\Protocols;
use RemoteRequest\RequestException;


/**
 * Class Answer
 * @package RemoteRequest\Protocols\Fsp
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
        $this->checkSize();
        $this->getHeader();
        $this->processHeader();
        $this->processContent();
        $this->checkResponse();
        return $this;
    }

    /**
     * @throws RequestException
     */
    protected function checkSize(): void
    {
        $loadSize = strlen($this->body);
        if (Protocols\Fsp::HEADER_SIZE > $loadSize) {
            throw new RequestException('Response too short');
        }
        if (Protocols\Fsp::MAX_PACKET_SIZE < $loadSize) {
            throw new RequestException('Response too large');
        }
    }

    protected function getHeader(): void
    {
        $this->header = substr($this->body, 0, Protocols\Fsp::HEADER_SIZE);
    }

    protected function processHeader(): void
    {
        $this->headCommand = Strings::cutter($this->header, 0, 1);
        $this->headChecksum = Strings::cutter($this->header, 1, 1);
        $this->headServerKey = Strings::cutter($this->header, 2, 2);
        $this->headSequence = Strings::cutter($this->header, 4, 2);
        $this->headDataLength = Strings::cutter($this->header, 6, 2);
        $this->headFilePosition = Strings::cutter($this->header, 8, 4);
    }

    protected function processContent(): void
    {
        $content = substr($this->body, Protocols\Fsp::HEADER_SIZE);
        $this->content = substr($content, 0, $this->getDataLength());
        $this->extra = substr($content, $this->getDataLength());
        $this->extra = (false !== $this->extra) ? $this->extra : '';
    }

    /**
     * Generate server checksum from data and compare them
     * @throws RequestException
     */
    protected function checkResponse(): void
    {
        $checksum = $this->computeCheckSum();
        // @codeCoverageIgnoreStart
        // necessary dumper - who can calculate checksums from their head?
        if ($this->canDump) {
            var_dump(['chksums',
                'calc_raw' => $checksum, 'calc_hex' => dechex($checksum), 'calc_bin' => decbin($checksum),
                'got_raw' => $this->headChecksum, 'got_hex' => dechex($this->headChecksum), 'got_bin' => decbin($this->headChecksum)
            ]);
        }
        // @codeCoverageIgnoreEnd
        if ($checksum != $this->headChecksum ) {
            throw new RequestException('Invalid checksum');
        }
    }

    public function getInitialSumChunk(): int
    {
        return 0;
    }

    public function getChecksumPacket(): string
    {
        $content = $this->body;
        $content[1] = chr(0); // null checksum
        return $content;
    }

    public function getCommand(): int
    {
        return (int)$this->headCommand;
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
        return (int)$this->headDataLength;
    }

    public function getFilePosition(): int
    {
        return (int)$this->headFilePosition;
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
