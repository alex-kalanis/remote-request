<?php

namespace kalanis\RemoteRequest\Protocols;


use kalanis\RemoteRequest;


/**
 * Class Restful
 * @package kalanis\RemoteRequest\Protocols
 * Properties for query to remote server - REST API
 */
class Restful extends Http
{
    protected function loadQuery(): RemoteRequest\Protocols\Dummy\Query
    {
        return new Restful\Query();
    }

    protected function loadAnswer(): RemoteRequest\Protocols\Dummy\Answer
    {
        $lib = new Restful\Answer();
        $lib->addStringDecoding(new Http\Answer\DecodeStrings\Chunked());
        $lib->addStringDecoding(new Http\Answer\DecodeStrings\Zipped());
        $lib->addStringDecoding(new Http\Answer\DecodeStrings\Compressed());
        $lib->addStringDecoding(new Http\Answer\DecodeStrings\Deflated());
        return $lib;
    }
}
