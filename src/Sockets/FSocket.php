<?php

namespace RemoteRequest\Sockets;

use RemoteRequest\RequestException;
use RemoteRequest\Schemas\ASchema;

/**
 * Network pointer to the remote server - method Fsocket
 */
class FSocket extends ASocket
{
    /**
     * @param ASchema $protocolWrapper
     * @return false|resource
     * @throws RequestException
     * @codeCoverageIgnore because accessing remote source
     */
    protected function remotePointer(ASchema $protocolWrapper)
    {
        // Make the request to the server
        // If possible, securely post using HTTPS, your PHP server will need to be SSL enabled
        $filePointer = fsockopen($protocolWrapper->getHostname(), $protocolWrapper->getPort(), $errno, $errstr, $protocolWrapper->getTimeout());

        if (!$filePointer) {
            throw new RequestException('Cannot establish connection');
        }
        return $filePointer;
    }
}
