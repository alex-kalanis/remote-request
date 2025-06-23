<?php

namespace kalanis\RemoteRequest\Protocols\Http\Answer\DecodeStrings;


use kalanis\RemoteRequest\Protocols;


/**
 * Class Chunked
 * @package kalanis\RemoteRequest\Protocols\Http\Answer\DecodeStrings
 * Glue chunked strings back to original
 */
class Chunked extends ADecoder
{
    public function getHeaderKey(): string
    {
        return 'Transfer-Encoding';
    }

    public function canDecode(string $header): bool
    {
        return 'chunked' == mb_strtolower($header);
    }

    /**
     * Repair chunked transport
     * do not ask how it works...
     * @param string $content
     * @return string
     * @link https://en.wikipedia.org/wiki/Chunked_transfer_encoding
     * @link https://tools.ietf.org/html/rfc2616#section-3.6
     */
    public function processDecode(string $content): string
    {
        $partialData = $content;
        $cleared = '';
        do {
            if (preg_match('#^(([0-9a-fA-F]+)\r\n)(.*)#m', $partialData, $matches)) {
                $segmentLength = hexdec($matches[2]);
                // skip bytes defined as chunk size and get next with length of chunk size
                $chunk = mb_substr($partialData, mb_strlen($matches[1]), intval($segmentLength));
                $cleared .= $chunk;
                // remove bytes with chunk size, chunk itself and ending crlf
                $partialData = mb_substr($partialData, mb_strlen($matches[1]) + mb_strlen($chunk) + mb_strlen(Protocols\Http::DELIMITER));
            } else {
                // @codeCoverageIgnoreStart
                $segmentLength = 0;
            }
            // @codeCoverageIgnoreEnd
        } while (0 < $segmentLength);
        $content = $cleared;
        return $content;
    }
}
