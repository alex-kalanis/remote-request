<?php

namespace kalanis\RemoteRequest\Protocols;


use kalanis\RemoteRequest;


/**
 * Class Https
 * @package kalanis\RemoteRequest\Protocols
 * Properties for query to remote server - method HTTP
 */
class Https extends Http
{
    protected function loadTarget(): RemoteRequest\Schemas\ASchema
    {
        return new RemoteRequest\Schemas\Ssl();
    }
}
