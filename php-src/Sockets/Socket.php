<?php

namespace kalanis\RemoteRequest\Sockets;


use kalanis\RemoteRequest\Interfaces\IConnectionParams;
use kalanis\RemoteRequest\RequestException;


/**
 * Class Socket
 * @package kalanis\RemoteRequest\Sockets
 * Network pointer to the remote server - method Socket
 * This one needs own processor - cannot write here via fwrite(), must use socket_sendto()!
 */
class Socket extends ASocket
{
    /**
     * @param IConnectionParams $params
     * @throws RequestException
     * @return resource
     * @codeCoverageIgnore because accessing remote source via internal socket
     */
    protected function remotePointer(IConnectionParams $params)
    {
        // Make the request to the server
        // If possible, securely post using HTTPS, your PHP server will need to be SSL enabled
        $filePointer = socket_create(AF_INET, SOCK_DGRAM, $params->getProtocolVersion());

        if (!$filePointer) {
            $errorCode = socket_last_error();
            $errorMessage = socket_strerror($errorCode);

            throw new RequestException($this->lang->rrSocketCannotConnect2($errorMessage), $errorCode);
        }
        return $filePointer;
    }
}
