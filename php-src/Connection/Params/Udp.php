<?php

namespace kalanis\RemoteRequest\Connection\Params;


use kalanis\RemoteRequest\Interfaces\ISchema;


/**
 * Properties for query to remote server - layer 2 protocol UDP
 * Class Udp
 * @package kalanis\RemoteRequest\Connection\Params
 */
class Udp extends AParams
{
    protected function getSchemaType(): string
    {
        return ISchema::SCHEMA_UDP;
    }

    public function getProtocolVersion(): int
    {
        return SOL_UDP;
    }
}
