<?php

namespace kalanis\RemoteRequest\Protocols\Http\Query;


use kalanis\RemoteRequest\Protocols\Http;


/**
 * Class AuthDigest
 * @package kalanis\RemoteRequest\Protocols\Http\Query
 * Message to the remote server compilation - protocol http
 */
class AuthDigest extends Http\Query
{
    use TAuthDigest;

    public function getData()
    {
        $this->authHeader();
        return parent::getData();
    }
}
