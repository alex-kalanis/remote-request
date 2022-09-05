<?php

namespace kalanis\RemoteRequest\Connection\Params;


use kalanis\RemoteRequest\Interfaces\ISchema;


/**
 * Class Tcp
 * @package kalanis\RemoteRequest\Connection\Params
 * Properties for query to remote server - layer 2 protocol TCP
 */
class Tcp extends AParams
{
    protected function getSchemaType(): string
    {
        return ISchema::SCHEMA_TCP;
    }

    public function getProtocolVersion(): int
    {
        return SOL_TCP;
    }
}
