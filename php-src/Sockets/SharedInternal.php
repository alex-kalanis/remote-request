<?php

namespace kalanis\RemoteRequest\Sockets;


use kalanis\RemoteRequest\Interfaces\IConnectionParams;
use kalanis\RemoteRequest\RequestException;


/**
 * Class SharedInternal
 * @package kalanis\RemoteRequest\Sockets
 * Pointer to the local source (file, memory)
 * Good one for testing (put inside the content you want to get)
 */
class SharedInternal extends ASocket
{
    /**
     * @param IConnectionParams $params
     * @throws RequestException
     * @return resource
     * @codeCoverageIgnore because accessing volume
     */
    protected function remotePointer(IConnectionParams $params)
    {
        $filePointer = fopen($params->getSchema() . $params->getHost(), 'r+');
        if (!$filePointer) {
            throw new RequestException($this->lang->rrSocketCannotConnect());
        }
        return $filePointer;
    }
}
