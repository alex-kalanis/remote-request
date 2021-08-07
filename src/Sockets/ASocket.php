<?php

namespace RemoteRequest\Sockets;


use RemoteRequest\RequestException;
use RemoteRequest\Schemas\ASchema;


/**
 * Class ASocket
 * @package RemoteRequest\Sockets
 * Network sockets to the remote server - base abstract method
 */
abstract class ASocket
{
    const SOCKET_INTERNAL = 1;
    const SOCKET_STREAM = 2;
    const SOCKET_FSOCKET = 3;
    const SOCKET_PFSOCKET = 4;
    const SOCKET_SOCKET = 5;

    protected $pointer = null;

    public function __destruct()
    {
        if (!empty($this->pointer)) {
            fclose($this->pointer);
            $this->pointer = null;
        }
    }

    /**
     * @param ASchema $protocolWrapper
     * @return resource|null
     * @throws RequestException
     */
    abstract protected function remotePointer(ASchema $protocolWrapper);

    /**
     * @param ASchema $protocolWrapper
     * @return resource|null
     * @throws RequestException
     */
    public function getResourcePointer(ASchema $protocolWrapper)
    {
        if (empty($this->pointer)) {
            $this->pointer = $this->remotePointer($protocolWrapper);
        } else {
            rewind($this->pointer);
        }
        return $this->pointer;
    }

    public static function getPointer(int $type = self::SOCKET_STREAM): ASocket
    {
        switch ($type) {
            case static::SOCKET_INTERNAL:
                return new SharedInternal();
            case static::SOCKET_STREAM:
                return new Stream();
            case static::SOCKET_PFSOCKET:
                return new PfSocket();
            case static::SOCKET_SOCKET:
                return new Socket();
            case static::SOCKET_FSOCKET:
            default:
                return new FSocket();
        }
    }
}
