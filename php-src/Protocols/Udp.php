<?php

namespace kalanis\RemoteRequest\Protocols;


use kalanis\RemoteRequest;


/**
 * Properties for query to remote server - raw UDP
 * Class Udp
 * @package kalanis\RemoteRequest\Protocols
 */
class Udp extends AProtocol
{
    protected function loadParams(): RemoteRequest\Connection\Params\AParams
    {
        return new RemoteRequest\Connection\Params\Udp();
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
