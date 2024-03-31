<?php

namespace kalanis\RemoteRequest\Connection\Params;


use kalanis\RemoteRequest\Interfaces\ISchema;


/**
 * Class Php
 * @package kalanis\RemoteRequest\Connection\Params
 * Properties for query to resource - wrapper for internal calls with available read and write props
 */
class Php extends AParams
{
    public const HOST_MEMORY = 'memory';
    public const HOST_TEMP = 'temp';

    protected function getSchemaType(): string
    {
        return ISchema::SCHEMA_PHP;
    }

    public function getPort(): ?int
    {
        return null;
    }

    public function getTimeout(): ?float
    {
        return null;
    }
}
