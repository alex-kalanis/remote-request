<?php

namespace RemoteRequest\Wrappers;

use RemoteRequest;

/**
 * Properties for query to remote server - layer 2 protocol UDP
 */
class Udp extends AWrapper
{
    public function getSchemaType(): string
    {
        return static::SCHEMA_UDP;
    }
}
