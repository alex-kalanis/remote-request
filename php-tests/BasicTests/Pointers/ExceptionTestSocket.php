<?php

namespace tests\BasicTests\Pointers;


use kalanis\RemoteRequest\Interfaces;
use kalanis\RemoteRequest\RequestException;
use kalanis\RemoteRequest\Sockets;


class ExceptionTestSocket extends Sockets\ASocket
{
    protected function remotePointer(Interfaces\IConnectionParams $params)
    {
        throw new RequestException($this->getRRLang()->rrSocketCannotConnect());
    }
}
