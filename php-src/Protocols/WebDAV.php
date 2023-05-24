<?php

namespace kalanis\RemoteRequest\Protocols;


use kalanis\RemoteRequest;


/**
 * Class WebDAV
 * @package kalanis\RemoteRequest\Protocols
 * Properties for query to remote server - WebDAV server
 * @link https://www.interval.cz/clanky/zaklinadlo-jmenem-webdav/
 * @link https://www.interval.cz/clanky/neco-malo-o-http-3/
 * @link https://www.interval.cz/clanky/openlitespeed-webserver-jako-http-3-proxy/
 */
class WebDAV extends Http
{
    protected function loadQuery(): RemoteRequest\Protocols\Dummy\Query
    {
        return new WebDAV\Query();
    }

    protected function loadAnswer(): RemoteRequest\Protocols\Dummy\Answer
    {
        return new WebDAV\Answer($this->rrLang);
    }
}
