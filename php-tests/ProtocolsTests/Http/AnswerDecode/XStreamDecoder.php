<?php

namespace tests\ProtocolsTests\Http\AnswerDecode;


use kalanis\RemoteRequest\Protocols\Http;


class XStreamDecoder
{
    use Http\Answer\DecodeStreams\TDecoding;

    public function getAllHeaders(): array
    {
        return [
            'Server' => ['PhpUnit/9.3.0'],
            'Content-Length' => ['25'],
            'Content-Type' => ['text/plain'],
            'Content-Encoding' => ['compress,deflate,gzip,custom'],
            'Transfer-Encoding' => ['chunked'],
            'Connection' => ['Closed'],
        ];
    }
}
