<?php

namespace RemoteRequest\Protocols;


use RemoteRequest;


/**
 * Properties for query to remote server - raw UDP
 * Class Udp
 * @package RemoteRequest\Protocols
 */
class Udp extends AProtocol
{
    protected function loadTarget(): RemoteRequest\Schemas\ASchema
    {
        return new RemoteRequest\Schemas\Udp();
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
