<?php

namespace RemoteRequest\Schemas;


use RemoteRequest;


/**
 * Class Php
 * @package RemoteRequest\Schemas
 * Properties for query to resource - wrapper for internal calls with available read and write props
 */
class Php extends ASchema
{
    const HOST_MEMORY = 'memory';
    const HOST_TEMP = 'temp';

    protected function getSchemaType(): string
    {
        return static::SCHEMA_PHP;
    }

    public function getPort(): ?int
    {
        return null;
    }

    public function getTimeout(): ?int
    {
        return null;
    }
}
