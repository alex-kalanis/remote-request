<?php

namespace RemoteRequest\Sockets;

use RemoteRequest\RequestException;
use RemoteRequest\Schemas\ASchema;

/**
 * Network pointer to the remote server - method PermaFsocket
 */
class PfSocket extends ASocket
{
    /**
     * @param ASchema $protocolWrapper
     * @return false|resource
     * @throws RequestException
     * @codeCoverageIgnore because accessing remote source
     */
    public function getRemotePointer(ASchema $protocolWrapper)
    {
        // Make the request to the server
        // If possible, securely post using HTTPS, your PHP server will need to be SSL enabled
        $filePointer = pfsockopen($protocolWrapper->getHostname(), $protocolWrapper->getPort(), $errno, $errstr, $protocolWrapper->getTimeout());

        if (!$filePointer) {
            throw new RequestException('Cannot establish connection');
        }
        return $filePointer;
    }
}
