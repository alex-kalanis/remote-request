<?php

namespace RemoteRequest\Protocols;

use RemoteRequest;

/**
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
