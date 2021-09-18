<?php

namespace kalanis\RemoteRequest\Protocols;


use kalanis\RemoteRequest;


/**
 * Class WebDAV
 * @package kalanis\RemoteRequest\Protocols
 * Properties for query to remote server - WebDAV server
 */
class WebDAV extends Http
{
    protected function loadQuery(): RemoteRequest\Protocols\Dummy\Query
    {
        return new WebDAV\Query();
    }

    protected function loadAnswer(): RemoteRequest\Protocols\Dummy\Answer
    {
        return new WebDAV\Answer();
    }
}
