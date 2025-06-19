<?php

namespace tests\ProtocolsTests\Http\AnswerDecode;


use kalanis\RemoteRequest\Protocols\Http;


class XStringDecoderCompress
{
    use Http\Answer\DecodeStrings\TDecoding;

    public function getAllHeaders(): array
    {
        return [
            'Server' => ['PhpUnit/9.3.0'],
            'Content-Length' => ['25'],
            'Content-Type' => ['text/plain'],
            'Content-Encoding' => ['compress'],
            'Transfer-Encoding' => ['chunked'],
            'Connection' => ['Closed'],
        ];
    }
}
