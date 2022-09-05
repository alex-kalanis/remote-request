<?php

namespace kalanis\RemoteRequest\Connection\Params;


use kalanis\RemoteRequest\Interfaces\ISchema;


/**
 * Class Ssl
 * @package kalanis\RemoteRequest\Connection\Params
 * Properties for query to remote server - layer 2 protocol TCP
 */
class Ssl extends AParams
{
    protected function getSchemaType(): string
    {
        return ISchema::SCHEMA_SSL;
    }

    public function getProtocolVersion(): int
    {
        return SOL_TCP;
    }
}
