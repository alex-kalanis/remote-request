<?php

namespace kalanis\RemoteRequest\Protocols\Http\Answer\DecodeStrings;


/**
 * Class Deflated
 * @package kalanis\RemoteRequest\Protocols\Http\Answer\DecodeStrings
 * Uncompress content - zlib by rfc-1950, rfc-1951
 * @requires extension zlib
 */
class Deflated extends ADecoder
{
    protected $contentEncoded = ['deflate', 'x-deflate'];

    public function processDecode(string $content): string
    {
        return gzinflate($content);
    }
}
