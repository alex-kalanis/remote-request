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
        $timeout = is_null($protocolWrapper->getTimeout()) ? 10.0 : floatval($protocolWrapper->getTimeout()); // do NOT ask - php7 + phpstan
        $filePointer = pfsockopen($protocolWrapper->getHostname(), intval($protocolWrapper->getPort()), $errno, $errStr, $timeout);

        if (!$filePointer) {
            throw new RequestException($this->lang->rrSocketCannotConnect());
        }
        if (!is_null($protocolWrapper->getTimeout())) {
            stream_set_timeout($filePointer, intval($protocolWrapper->getTimeout()));
        }
        return $filePointer;
    }
}
