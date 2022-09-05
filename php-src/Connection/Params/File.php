<?php

namespace kalanis\RemoteRequest\Connection\Params;


use kalanis\RemoteRequest\Interfaces\ISchema;


/**
 * Class File
 * @package kalanis\RemoteRequest\Connection\Params
 * Properties for query to resource - wrapper for file
 */
class File extends AParams
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
