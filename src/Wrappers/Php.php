<?php

namespace RemoteRequest\Wrappers;

use RemoteRequest;

/**
 * Properties for query to resource - wrapper for internal calls with available read and write props
 */
class Php extends AWrapper
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
