<?php

namespace kalanis\RemoteRequest\Protocols;


use kalanis\RemoteRequest;


/**
 * Class Http
 * @package kalanis\RemoteRequest\Protocols
 * Properties for query to remote server - method HTTP
 */
class Http extends AProtocol
{
    const DELIMITER = "\r\n";

    protected function loadTarget(): RemoteRequest\Schemas\ASchema
    {
        return new RemoteRequest\Schemas\Tcp();
    }

    protected function loadQuery(): RemoteRequest\Protocols\Dummy\Query
    {
        return new Http\Query();
    }

    protected function loadAnswer(): RemoteRequest\Protocols\Dummy\Answer
    {
        return new Http\Answer();
    }
}