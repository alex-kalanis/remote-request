<?php

namespace kalanis\RemoteRequest\Schemas;


use kalanis\RemoteRequest\Interfaces\ISchema;


/**
 * Class File
 * @package kalanis\RemoteRequest\Schemas
 * Properties for query to resource - wrapper for file
 */
class File extends ASchema
{
    protected function getSchemaType(): string
    {
        return ISchema::SCHEMA_FILE;
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
