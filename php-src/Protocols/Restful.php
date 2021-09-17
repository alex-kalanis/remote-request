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
        return new Restful\Answer();
    }
}
