<?php

namespace kalanis\RemoteRequest\Protocols\Http\Query;


use kalanis\RemoteRequest\Protocols\Http;


/**
 * Class AuthBasic
 * @package kalanis\RemoteRequest\Protocols\Http\Query
 * Message to the remote server compilation - protocol http
 */
class AuthBasic extends Http\Query
{
    use TAuthBasic;

    public function getData(): string
    {
        $this->authHeader();
        return parent::getData();
    }
}
