<?php

namespace RemoteRequest\Schemas;

use RemoteRequest;

/**
 * Properties for query to remote server - layer 2 protocol TCP
 */
class Ssl extends ASchema
{
    protected function getSchemaType(): string
    {
        return static::SCHEMA_SSL;
    }

    public function getProtocol(): int
    {
        return SOL_TCP;
    }
}
