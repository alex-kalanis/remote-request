<?php

namespace kalanis\RemoteRequest\Protocols;


use kalanis\RemoteRequest;


/**
 * Class Tcp
 * @package kalanis\RemoteRequest\Protocols
 * Properties for query to remote server - raw TCP
 */
class Tcp extends AProtocol
{
    protected function loadParams(): RemoteRequest\Connection\Params\AParams
    {
        return new RemoteRequest\Connection\Params\Tcp();
    }

    protected function loadQuery(): RemoteRequest\Protocols\Dummy\Query
    {
        return new Dummy\Query();
    }

    protected function loadAnswer(): RemoteRequest\Protocols\Dummy\Answer
    {
        return new Dummy\Answer();
    }
}
