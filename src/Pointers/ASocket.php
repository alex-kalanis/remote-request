<?php

namespace RemoteRequest\Pointers;

use RemoteRequest\RequestException;
use RemoteRequest\Wrappers\AWrapper;

/**
 * Network pointer to the remote server - base abstract method
 */
abstract class ASocket
{
    const SOCKET_INTERNAL = 1;
    const SOCKET_STREAM = 2;
    const SOCKET_FSOCKET = 3;
    const SOCKET_PFSOCKET = 4;
    const SOCKET_SOCKET = 5;

    /**
     * @param AWrapper $protocolWrapper
     * @return resource
     * @throws RequestException
     */
    abstract public function getRemotePointer(AWrapper $protocolWrapper);

    public static function getPointer(int $type = self::SOCKET_STREAM): ASocket
    {
        switch ($type) {
            case static::SOCKET_INTERNAL:
                return new SharedInternal();
            case static::SOCKET_STREAM:
                return new Stream();
            case static::SOCKET_PFSOCKET:
                return new Pfsocket();
            case static::SOCKET_SOCKET:
                return new Socket();
            case static::SOCKET_FSOCKET:
            default:
                return new Fsocket();
        }
    }
}
