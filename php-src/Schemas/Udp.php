<?php

namespace kalanis\RemoteRequest\Schemas;


use kalanis\RemoteRequest\Interfaces\ISchema;


/**
 * Properties for query to remote server - layer 2 protocol UDP
 * Class Udp
 * @package kalanis\RemoteRequest\Schemas
 */
class Udp extends ASchema
{
    protected function getSchemaType(): string
    {
        return ISchema::SCHEMA_UDP;
    }

    public function getProtocol(): int
    {
        return SOL_UDP;
    }
}
