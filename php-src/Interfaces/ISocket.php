<?php

namespace kalanis\RemoteRequest\Interfaces;


/**
 * Class ISocket
 * @package kalanis\RemoteRequest\Interfaces
 * Which socket types are available
 */
interface ISocket
{
    public const SOCKET_INTERNAL = 1;
    public const SOCKET_STREAM = 2;
    public const SOCKET_FSOCKET = 3;
    public const SOCKET_PFSOCKET = 4;
    public const SOCKET_SOCKET = 5;
}
