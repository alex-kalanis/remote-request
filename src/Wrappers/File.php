<?php

namespace RemoteRequest\Wrappers;

use RemoteRequest;

/**
 * Properties for query to resource - wrapper for file
 */
class File extends AWrapper
{
    protected function getSchemaType(): string
    {
        return static::SCHEMA_FILE;
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
