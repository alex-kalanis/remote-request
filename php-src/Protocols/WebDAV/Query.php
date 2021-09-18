<?php

namespace kalanis\RemoteRequest\Protocols\WebDAV;


use kalanis\RemoteRequest\Protocols;


/**
 * Class Query
 * @package kalanis\RemoteRequest\Protocols\WebDAV
 * Simple WebDAV query to remote source
 */
class Query extends Protocols\Http\Query
{
    /** @var string[] */
    protected $availableMethods = ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'COPY', 'LOCK', 'MKCOL', 'MOVE', 'PROPFIND', 'PROPPATCH', 'UNLOCK'];

    public function isInline(): bool
    {
        return false;
    }
}
