<?php

namespace RemoteRequest\Protocols;

use RemoteRequest;

/**
 * Properties for query to remote server - raw UDP
 */
class Udp extends AProtocol
{
    protected function loadTarget(): RemoteRequest\Wrappers\AWrapper
    {
        return new RemoteRequest\Wrappers\Udp();
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
