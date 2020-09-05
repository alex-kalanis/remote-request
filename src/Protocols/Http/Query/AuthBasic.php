<?php

namespace RemoteRequest\Protocols\Http\Query;

use RemoteRequest\Protocols\Http;

/**
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