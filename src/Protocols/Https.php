<?php

namespace RemoteRequest\Protocols;


use RemoteRequest;


/**
 * Class Https
 * @package RemoteRequest\Protocols
 * Properties for query to remote server - method HTTP
 */
class Https extends Http
{
    protected function loadTarget(): RemoteRequest\Schemas\ASchema
    {
        return new RemoteRequest\Schemas\Ssl();
    }
}
