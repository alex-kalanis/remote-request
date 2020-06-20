<?php

namespace RemoteRequest\Schemas;

use RemoteRequest;

/**
 * Properties for query to remote server - layer 2 protocol SCTP
 * @link https://en.wikipedia.org/wiki/Stream_Control_Transmission_Protocol
 *
 */
class Sctp extends ASchema
{
    protected function getSchemaType(): string
    {
        return static::SCHEMA_SCTP;
    }
}
