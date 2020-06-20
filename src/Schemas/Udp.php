<?php

namespace RemoteRequest\Schemas;

use RemoteRequest;

/**
 * Properties for query to remote server - layer 2 protocol UDP
 */
class Udp extends ASchema
{
    protected function getSchemaType(): string
    {
        return static::SCHEMA_UDP;
    }

    public function getProtocol(): int
    {
        return SOL_UDP;
    }
}
