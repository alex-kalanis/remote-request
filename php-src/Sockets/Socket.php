<?php

namespace kalanis\RemoteRequest\Sockets;


use kalanis\RemoteRequest\RequestException;
use kalanis\RemoteRequest\Schemas\ASchema;


/**
 * Class Socket
 * @package kalanis\RemoteRequest\Sockets
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
    protected function remotePointer(ASchema $protocolWrapper)
    {
        // Make the request to the server
        // If possible, securely post using HTTPS, your PHP server will need to be SSL enabled
        $filePointer = socket_create(AF_INET, SOCK_DGRAM, $protocolWrapper->getProtocol());

        if (!$filePointer) {
            $errorCode = socket_last_error();
            $errorMessage = socket_strerror($errorCode);

            throw new RequestException($this->lang->rrSocketCannotConnect2($errorMessage), $errorCode);
        }
        return $filePointer;
    }
}
