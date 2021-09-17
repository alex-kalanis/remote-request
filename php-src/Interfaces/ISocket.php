<?php

namespace kalanis\RemoteRequest\Interfaces;


/**
 * Class ISocket
 * @package kalanis\RemoteRequest\Interfaces
 * Which socket types are available
 */
interface ISocket
{
    const SOCKET_INTERNAL = 1;
    const SOCKET_STREAM = 2;
    const SOCKET_FSOCKET = 3;
    const SOCKET_PFSOCKET = 4;
    const SOCKET_SOCKET = 5;
}
