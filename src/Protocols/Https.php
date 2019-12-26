<?php

namespace RemoteRequest\Protocols;

use RemoteRequest;

/**
 * Properties for query to remote server - method HTTP
 */
class Https extends Http
{
    protected function loadTarget(): RemoteRequest\Wrappers\AWrapper
    {
        return new RemoteRequest\Wrappers\Ssl();
    }
}
