<?php

namespace kalanis\RemoteRequest\Protocols\Http\Answer\DecodeStrings;


/**
 * Class Compressed
 * @package kalanis\RemoteRequest\Protocols\Http\Answer\DecodeStringsencoded
 * Unzip zipped content - Lempel-Ziv-Welch (LZW)
 * @requires extension zlib
 */
class Compressed extends ADecoder
{
    protected $contentEncoded = ['compress', 'x-compress'];

    public function processDecode(string $content): string
    {
        return strval(gzuncompress($content));
    }
}
