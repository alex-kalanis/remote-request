<?php

namespace kalanis\RemoteRequest\Sockets;


use kalanis\RemoteRequest\RequestException;
use kalanis\RemoteRequest\Schemas\ASchema;


/**
 * Class SharedInternal
 * @package kalanis\RemoteRequest\Sockets
 * Pointer to the local source (file, memory)
 * Good one for testing (put inside the content you want to get)
 */
class SharedInternal extends ASocket
{
    /**
     * @param ASchema $protocolWrapper
     * @return bool|resource|null
     * @throws RequestException
     * @codeCoverageIgnore because accessing volume
     */
    protected function remotePointer(ASchema $protocolWrapper)
    {
        $filePointer = fopen($protocolWrapper->getHostname(), 'r+');
        if (!$filePointer) {
            throw new RequestException($this->lang->rrSocketCannotConnect());
        }
        return $filePointer;
    }
}
