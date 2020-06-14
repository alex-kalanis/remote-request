<?php

namespace RemoteRequest\Protocols;

use RemoteRequest;

/**
 * Properties for query to remote server - raw SCTP
 * SCTP means Stream Control Transmission Protocol
 * It's hybrid between UDP and TCP
 * @link https://en.wikipedia.org/wiki/Stream_Control_Transmission_Protocol
 * @link https://tools.ietf.org/html/rfc4960
 */
class Sctp extends AProtocol
{
    protected function loadTarget(): RemoteRequest\Wrappers\AWrapper
    {
        return new RemoteRequest\Wrappers\Sctp();
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
