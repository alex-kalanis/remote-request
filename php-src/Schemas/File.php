<?php

namespace kalanis\RemoteRequest\Schemas;


/**
 * Class File
 * @package kalanis\RemoteRequest\Schemas
 * Properties for query to resource - wrapper for file
 */
class File extends ASchema
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
