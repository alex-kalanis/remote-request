<?php

namespace kalanis\RemoteRequest\Schemas;


use kalanis\RemoteRequest\Interfaces\ISchema;


/**
 * Class Ssl
 * @package kalanis\RemoteRequest\Schemas
 * Properties for query to remote server - layer 2 protocol TCP
 */
class Ssl extends ASchema
{
    protected function getSchemaType(): string
    {
        return ISchema::SCHEMA_SSL;
    }

    public function getProtocol(): int
    {
        return SOL_TCP;
    }
}
