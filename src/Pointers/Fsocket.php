<?php

namespace RemoteRequest\Pointers;

use RemoteRequest\RequestException;
use RemoteRequest\Wrappers\AWrapper;

/**
 * Network pointer to the remote server - method Fsocket
 */
class Fsocket extends ASocket
{
    /**
     * @param AWrapper $protocolWrapper
     * @return false|resource
     * @throws RequestException
     * @codeCoverageIgnore because accessing remote source
     */
    public function getRemotePointer(AWrapper $protocolWrapper)
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
