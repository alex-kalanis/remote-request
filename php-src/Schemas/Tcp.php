<?php

namespace kalanis\RemoteRequest\Schemas;


use kalanis\RemoteRequest\Interfaces\ISchema;


/**
 * Class Tcp
 * @package kalanis\RemoteRequest\Schemas
 * Properties for query to remote server - layer 2 protocol TCP
 */
class Tcp extends ASchema
{
    protected function getSchemaType(): string
    {
        return ISchema::SCHEMA_TCP;
    }

    public function getProtocol(): int
    {
        return SOL_TCP;
    }
}
