<?php

namespace tests\BasicTests\Pointers;


use kalanis\RemoteRequest\Interfaces;
use kalanis\RemoteRequest\Sockets;


class EmptyTestSocket extends Sockets\ASocket
{
    protected function remotePointer(Interfaces\IConnectionParams $params)
    {
        return null;
    }
}
