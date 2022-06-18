<?php

namespace kalanis\RemoteRequest\Protocols\Http;


use kalanis\RemoteRequest\Interfaces\IRRTranslations;
use kalanis\RemoteRequest\Protocols;
use kalanis\RemoteRequest\RequestException;


/**
 * Class Answer
 * @package kalanis\RemoteRequest\Protocols\Http
 * Process server's answer - parse http
 *
 * hints:
 *  - zip, compress, deflate -> now only on blocks - up to 16MB of content
 */
class Answer extends Protocols\Dummy\Answer
{
    use Answer\DecodeStreams\TDecoding;
    use Answer\DecodeStrings\TDecoding;

    /** @var IRRTranslations */
    protected $lang = null;
    /** @var string[][] */
    protected $headers = [];
    /** @var int */
    protected $code = 0;
    /** @var int */
    protected $maxHeaderSize = 17000; // over 16384 - 16K
    /** @var int */
    protected $maxStringSize = 10000;
    /** @var int<0, max> */
    protected $seekSize = 1024; // in how big block we will look for delimiters
    /** @var int */
    protected $seekPos = 1000; // must be reasonably lower than seekSize - because it's necessary to find delimiters even on edges

    public function __construct(IRRTranslations $lang)
    {
        $this->lang = $lang;
    }

    protected function clearValues(): void
    {
        $this->headers = [];
        $this->code = 0;
        $this->body = null;
    }

    /**
     * @param resource|string|null $message
     * @throws RequestException
     * @return $this
     */
    public function setResponse($message): parent
    {
        $this->clearValues();
        if (is_resource($message)) {
            $this->processStreamResponse($message);
        } elseif (is_string($message)) {
            $this->processStringResponse($message);
        }
        return $this;
    }

    /**
     * @param resource $message
     * @throws RequestException
     */
    protected function processStreamResponse($message): void
    {
        $headerSize = $position = 0;
        $onlyHeader = false;
        rewind($message);
        while ($data = fread($message, $this->seekSize)) {
            if (false !== $pos = mb_strpos($data, Protocols\Http::DELIMITER . Protocols\Http::DELIMITER)) {
                $headerSize = $position + $pos;
                break;
            }
            $position += $this->seekPos;
            fseek($message, $position);
        }
        if (0 == $headerSize) {
            $headerSize = $position;
            $onlyHeader = true;
        }
        if ($headerSize > $this->maxHeaderSize) {
            throw new RequestException($this->lang->rrHttpAnswerHeaderTooLarge($this->maxHeaderSize, $headerSize));
        }
        rewind($message);
        $this->parseHeader((string)stream_get_contents($message, $headerSize, 0));
        if ($onlyHeader) {
            return;
        }
        // phpstan is only blind...
        $headerSize += mb_strlen(Protocols\Http::DELIMITER . Protocols\Http::DELIMITER); // @phpstan-ignore-line
        if ($this->bodySizeMightBeTooLarge()) {
            $this->processStreamBody($message, $headerSize);
        } else {
            $this->processStringBody((string)stream_get_contents($message, -1, $headerSize));
        }
    }

    protected function processStringResponse(string $data): void
    {
        if (false !== $split = mb_strpos($data, Protocols\Http::DELIMITER . Protocols\Http::DELIMITER)) {
            list($header, $body) = explode(Protocols\Http::DELIMITER . Protocols\Http::DELIMITER, $data, 2);
            $this->parseHeader($header);
            if ($this->bodySizeMightBeTooLarge()) {
                $this->processStreamBodyFromString($body);
            } else {
                $this->processStringBody($body);
            }
        } else {
            $this->parseHeader($data);
            $this->body = null;
        }
    }

    protected function parseHeader(string $header): void
    {
        $lines = explode(Protocols\Http::DELIMITER, $header);
        foreach ($lines as $line) {
            if (preg_match('/HTTP\/[^\s]+\s([0-9]{3})\s(.+)/ui', $line, $matches)) {
                $this->code = $matches[1];
            } else {
                if (mb_strlen($line) && (false !== mb_strpos($line, ':'))) {
                    list($key, $value) = explode(': ', $line);
                    if (!isset($this->headers[$key])) {
                        $this->headers[$key] = [];
                    }
                    $this->headers[$key][] = $value;
                }
            }
        }
    }

    protected function bodySizeMightBeTooLarge(): bool
    {
        return intval($this->getHeader('Content-Length', '0')) > $this->maxStringSize;
    }

    /**
     * @param resource $body
     * @param int $headerSize
     */
    protected function processStreamBody($body, int $headerSize): void
    {
        $res = Protocols\Helper::getTempStorage();
        stream_copy_to_stream($body, $res, -1, $headerSize);
        rewind($res);
        $this->body = $this->processStreamDecode($res);
    }

    protected function processStreamBodyFromString(string $body): void
    {
        $res = Protocols\Helper::getTempStorage();
        fwrite($res, $body);
        rewind($res);
        $this->body = $this->processStreamDecode($res);
    }

    protected function processStringBody(string $body): void
    {
        $res = Protocols\Helper::getMemStorage();
        fwrite($res, $this->processStringDecode($body));
        rewind($res);
        $this->body = $res;
    }

    public function getHeader(string $key, ?string $default = null): ?string
    {
        return isset($this->headers[$key])? (string)reset($this->headers[$key]) : $default;
    }

    /**
     * @param string $key
     * @return string[]
     */
    public function getHeaders(string $key): array
    {
        return isset($this->headers[$key])? $this->headers[$key] : [];
    }

    /**
     * Dump all obtained headers - usually for DEVEL
     * @return array<string, array<string>>
     */
    public function getAllHeaders(): array
    {
        return $this->headers;
    }

    public function getCode(): int
    {
        return intval($this->code);
    }

    public function isSuccessful(): bool
    {
        return in_array($this->getCode(), [200, 206]);
    }
}
