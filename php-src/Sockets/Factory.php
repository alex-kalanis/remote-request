<?php

namespace kalanis\RemoteRequest\Sockets;


use kalanis\RemoteRequest\Interfaces\IRRTranslations;
use kalanis\RemoteRequest\Interfaces\ISocket;


/**
 * Class Factory
 * @package kalanis\RemoteRequest\Sockets
 * Network sockets to the remote server - which can be used
 */
class Factory
{
    public static function getPointer(int $type = ISocket::SOCKET_STREAM, ?IRRTranslations $lang = null): ASocket
    {
        switch ($type) {
            case ISocket::SOCKET_INTERNAL:
                return new SharedInternal($lang);
            case ISocket::SOCKET_STREAM:
                return new Stream($lang);
            case ISocket::SOCKET_PFSOCKET:
                return new PfSocket($lang);
            case ISocket::SOCKET_SOCKET:
                return new Socket($lang);
            case ISocket::SOCKET_FSOCKET:
            default:
                return new FSocket($lang);
        }
    }
}
