<?php

namespace RemoteRequest\Pointers;

use RemoteRequest\RequestException;
use RemoteRequest\Wrappers\AWrapper;

/**
 * Network pointer to the remote server - base abstract method
 */
abstract class APointer
{
    const POINTER_INTERNAL = 1;
    const POINTER_STREAM = 2;
    const POINTER_FSOCKET = 3;
    const POINTER_PFSOCKET = 4;

    /**
     * @param AWrapper $protocolWrapper
     * @return resource
     * @throws RequestException
     */
    abstract public function getRemotePointer(AWrapper $protocolWrapper);

    public static function getPointer(int $type = self::POINTER_STREAM): APointer
    {
        switch ($type) {
            case static::POINTER_INTERNAL:
                return new SharedInternal();
            case static::POINTER_STREAM:
                return new Stream();
            case static::POINTER_PFSOCKET:
                return new Pfsocket();
            case static::POINTER_FSOCKET:
            default:
                return new Fsocket();
        }
    }
}
