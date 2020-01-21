<?php

namespace RemoteRequest\Wrappers;

use RemoteRequest;

/**
 * Properties for query to remote server - layer 2 protocol TCP
 */
class Tcp extends AWrapper
{
    protected function getSchemaType(): string
    {
        return static::SCHEMA_TCP;
    }
}
