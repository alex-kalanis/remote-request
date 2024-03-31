<?php

namespace kalanis\RemoteRequest\Protocols\Fsp;


use kalanis\RemoteRequest\Interfaces\IRRTranslations;
use kalanis\RemoteRequest\Protocols;
use kalanis\RemoteRequest\RequestException;
use kalanis\RemoteRequest\Traits\TLang;


/**
 * Class Answer
 * @package RemoteRequest\Protocols\Fsp
 * Process server's answer - FSP packet
 */
class Answer extends Protocols\Dummy\Answer
{
    use TLang;
    use Traits\THeader;
    use Traits\TChecksum;

    protected int $headChecksum = 0;
    protected int $headCommand = 0;
    protected int $headServerKey = 0;
    protected int $headSequence = 0;
    protected int $headDataLength = 0;
    protected int $headFilePosition = 0;
    protected string $header = '';
    protected string $content = '';
    protected string $extra = '';

    public bool $canDump = false; // for dump info about checksums

    public function __construct(?IRRTranslations $lang = null)
    {
        $this->setRRLang($lang);
    }

    /**
     * @throws RequestException
     * @return Answer
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
        // @phpstan-ignore-next-line
        $loadSize = is_resource($this->body) ? fstat($this->body)['size'] : strlen(strval($this->body));
        if (Protocols\Fsp::HEADER_SIZE > $loadSize) {
            throw new RequestException($this->getRRLang()->rrFspResponseShort($loadSize));
        }
        if (Protocols\Fsp::MAX_PACKET_SIZE < $loadSize) {
            throw new RequestException($this->getRRLang()->rrFspResponseLarge($loadSize));
        }
    }

    protected function getHeader(): void
    {
        $this->header = is_resource($this->body)
            ? strval(stream_get_contents($this->body, Protocols\Fsp::HEADER_SIZE, 0))
            : strval(substr(strval($this->body), 0, Protocols\Fsp::HEADER_SIZE))
        ;
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
        $content = is_resource($this->body)
            ? strval(stream_get_contents($this->body, -1, Protocols\Fsp::HEADER_SIZE))
            : strval(substr(strval($this->body), Protocols\Fsp::HEADER_SIZE))
        ;
        $this->content = strval(substr($content, 0, $this->getDataLength()));
        $this->extra = strval(substr($content, $this->getDataLength()));
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
            throw new RequestException($this->getRRLang()->rrFspInvalidChecksum($checksum, $this->headChecksum));
        }
    }

    public function getInitialSumChunk(): int
    {
        return 0;
    }

    public function getChecksumPacket(): string
    {
        $content = $this->header . $this->content . $this->extra ;
        $content[1] = chr(0); // null checksum
        return $content;
    }

    public function getCommand(): int
    {
        return intval($this->headCommand);
    }

    public function getKey(): int
    {
        return intval($this->headServerKey);
    }

    public function getSequence(): int
    {
        return intval($this->headSequence);
    }

    public function getDataLength(): int
    {
        return intval($this->headDataLength);
    }

    public function getFilePosition(): int
    {
        return intval($this->headFilePosition);
    }

    public function getContent(): string
    {
        return strval($this->content);
    }

    public function getExtraData(): string
    {
        return strval($this->extra);
    }
}
