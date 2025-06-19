<?php

namespace tests\ProtocolsTests\Http\AnswerDecode;


use kalanis\RemoteRequest\Protocols\Http;


class XDecoder extends Http\Answer\DecodeStreams\ADecoder
{
    protected array $contentEncoded = ['custom'];

    public function processDecode($content)
    {
        fseek($content, 0, SEEK_END);
        fwrite($content, '---!!!');
        return $content;
    }
}
