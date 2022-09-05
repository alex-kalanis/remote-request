<?php

namespace kalanis\RemoteRequest\Sockets;


use kalanis\RemoteRequest\Interfaces\IConnectionParams;
use kalanis\RemoteRequest\RequestException;


/**
 * Class PfSocket
 * @package kalanis\RemoteRequest\Sockets
 * Network pointer to the remote server - method PermaFsocket
 */
class PfSocket extends ASocket
{
    /**
     * @param IConnectionParams $params
     * @throws RequestException
     * @return resource
     * @codeCoverageIgnore because accessing remote source
     */
    protected function remotePointer(IConnectionParams $params)
    {
        // Make the request to the server
        // If possible, securely post using HTTPS, your PHP server will need to be SSL enabled
        $timeout = is_null($params->getTimeout()) ? 10.0 : floatval($params->getTimeout()); // do NOT ask - php7 + phpstan
        $filePointer = pfsockopen($params->getSchema() . $params->getHost(), intval($params->getPort()), $errno, $errStr, $timeout);

        if (!$filePointer) {
            throw new RequestException($this->lang->rrSocketCannotConnect());
        }
        if (!is_null($params->getTimeout())) {
            stream_set_timeout($filePointer, intval($params->getTimeout()));
        }
        return $filePointer;
    }
}
