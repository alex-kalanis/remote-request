<?php

namespace kalanis\RemoteRequest\Protocols\Http\Answer\DecodeStrings;


/**
 * Class Zipped
 * @package kalanis\RemoteRequest\Protocols\Http\Answer\DecodeStrings
 * search and add zipped content first
 * Unzip zipped content - Lempel-Ziv coding (LZ77); contains crc32
 */
class Zipped extends ADecoder
{
    protected $contentEncoded = ['gzip', 'x-gzip'];

    public function processDecode(string $content): string
    {
        return strval(gzdecode($content));
    }
}
