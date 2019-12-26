<?php

namespace RemoteRequest\Protocols\Http;

use RemoteRequest\Protocols;

/**
 * Process server's answer - parse http
 */
class Answer extends Protocols\Dummy\Answer
{
    /** @var string[][] */
    protected $headers = [];
    protected $code = 0;

    protected function clearValues(): void
    {
        $this->headers = [];
        $this->code = 0;
        $this->body = '';
    }

    public function setResponse(string $message)
    {
        $this->clearValues();
        if (false !== mb_strpos($message, Protocols\Http::DELIMITER . Protocols\Http::DELIMITER)) {
            list($header, $body) = explode(Protocols\Http::DELIMITER . Protocols\Http::DELIMITER, $message, 2);
            $this->parseHeader($header);
            $this->parseBody($body);
        } else {
            $this->parseHeader($message);
            $this->body = '';
        }
        return $this;
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

    /**
     * Parse body of query
     * Due changes by content encoding there is a way to expand to the original content
     * @param string $content
     * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Encoding
     */
    protected function parseBody(string $content): void
    {
        $transfer = $this->getHeader('Transfer-Encoding');
        if (!is_null($transfer) && ('chunked' == mb_strtolower($transfer))) {
            $content = $this->parseChunked($content);
        }
        $encode = $this->encodings($this->getHeader('Content-Encoding'));
        foreach ($encode as $coding) {
            if (in_array($coding, ['gzip', 'x-gzip'])) {
                $content = $this->parseZipped($content);
            }
            if (in_array($coding, ['compress', 'x-compress'])) {
                $content = $this->parseCompressed($content);
            }
            if (in_array($coding, ['deflate', 'x-deflate'])) {
                $content = $this->parseDeflated($content);
            }
        }
        $this->body = $content;
    }

    /**
     * Extract encodings from its compiled content
     * @param string|null $encode
     * @return string[]
     */
    protected function encodings($encode)
    {
        if (empty($encode)) {
            return [];
        }
        return array_map(function ($enc) {
            return trim(mb_strtolower($enc));
        }, explode(',', $encode));
    }

    /**
     * Repair chunked transport
     * do not ask how it works...
     * @param string $content
     * @return string
     * @link https://en.wikipedia.org/wiki/Chunked_transfer_encoding
     * @link https://tools.ietf.org/html/rfc2616#section-3.6
     */
    protected function parseChunked(string $content): string
    {
        $partialData = $content;
        $cleared = '';
        do {
            preg_match('#^(([0-9a-fA-F]+)\r\n)(.*)#m', $partialData, $matches);
            $segmentLength = hexdec($matches[2]);
            // skip bytes defined as chunk size and get next with length of chunk size
            $chunk = mb_substr($partialData, mb_strlen($matches[1]), $segmentLength);
            $cleared .= $chunk;
            // remove bytes with chunk size, chunk itself and ending crlf
            $partialData = mb_substr($partialData, mb_strlen($matches[1]) + mb_strlen($chunk) + mb_strlen(Protocols\Http::DELIMITER));
        } while ($segmentLength > 0);
        $content = $cleared;
        return $content;
    }

    /**
     * Unzip zipped content - Lempel-Ziv coding (LZ77); contains crc32
     * @param string $content
     * @return string
     */
    protected function parseZipped(string $content): string
    {
        return gzdecode($content);
    }

    /**
     * Unzip zipped content - Lempel-Ziv-Welch (LZW)
     * @param string $content
     * @return string
     */
    protected function parseCompressed(string $content): string
    {
        return gzuncompress($content);
    }

    /**
     * Uncompress content - zlib by rfc-1950, rfc-1951
     * @param string $content
     * @return string
     */
    protected function parseDeflated(string $content): string
    {
        return gzinflate($content);
    }

    public function getHeader($key, $default = null): ?string
    {
        return isset($this->headers[$key])? (string)reset($this->headers[$key]) : $default;
    }

    /**
     * @param string $key
     * @return string[]
     */
    public function getHeaders(string $key)
    {
        return isset($this->headers[$key])? $this->headers[$key] : [];
    }

    /**
     * Dump all obtained headers - usually for DEVEL
     * @return string[][]
     */
    public function getAllHeaders()
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