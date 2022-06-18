<?php

namespace kalanis\RemoteRequest\Sockets;


use kalanis\RemoteRequest\RequestException;
use kalanis\RemoteRequest\Schemas\ASchema;


/**
 * Class PfSocket
 * @package kalanis\RemoteRequest\Sockets
 * Network pointer to the remote server - method PermaFsocket
 */
class PfSocket extends ASocket
{
    /**
     * @param ASchema $protocolWrapper
     * @throws RequestException
     * @return resource
     * @codeCoverageIgnore because accessing remote source
     */
    protected function remotePointer(ASchema $protocolWrapper)
    {
        // Make the request to the server
        // If possible, securely post using HTTPS, your PHP server will need to be SSL enabled
        $filePointer = pfsockopen($protocolWrapper->getHostname(), intval($protocolWrapper->getPort()), $errno, $errStr, $protocolWrapper->getTimeout());

        if (!$filePointer) {
            throw new RequestException($this->lang->rrSocketCannotConnect());
        }
        return $filePointer;
    }
}
