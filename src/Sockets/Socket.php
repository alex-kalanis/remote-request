<?php

namespace RemoteRequest\Sockets;

use RemoteRequest\RequestException;
use RemoteRequest\Schemas\ASchema;

/**
 * Network pointer to the remote server - method Socket
 * This one needs own processor - cannot write here via fwrite(), must use socket_sendto()!
 */
class Socket extends ASocket
{
    /**
     * @param ASchema $protocolWrapper
     * @return resource
     * @throws RequestException
     * @codeCoverageIgnore because accessing remote source via internal socket
     */
    public function getRemotePointer(ASchema $protocolWrapper)
    {
        // Make the request to the server
        // If possible, securely post using HTTPS, your PHP server will need to be SSL enabled
        $filePointer = socket_create(AF_INET, SOCK_DGRAM, $protocolWrapper->getProtocol());

        if (!$filePointer) {
            $errorCode = socket_last_error();
            $errorMesssage = socket_strerror($errorCode);

            throw new RequestException('Cannot establish connection:' . $errorMesssage, $errorCode);
        }
        return $filePointer;
    }
}
