<?php

namespace kalanis\RemoteRequest\Pointers;


use kalanis\RemoteRequest\Interfaces\IConnectionParams;
use kalanis\RemoteRequest\Protocols\Helper;
use kalanis\RemoteRequest\RequestException;


/**
 * Class SocketProcessor
 * @package kalanis\RemoteRequest\Pointers
 * Query to the remote server - read into provided output
 * @codeCoverageIgnore because accessing remote source via internal socket
 */
class SocketProcessor extends Processor
{
    /**
     * @param resource $filePointer
     * @param IConnectionParams $params
     * @throws RequestException
     * @return $this
     */
    protected function writeRequest($filePointer, IConnectionParams $params): parent
    {
        $input = $this->remoteQuery ? strval(stream_get_contents($this->remoteQuery->getData(), -1, 0)) : '';
        $result = socket_sendto($filePointer, $input, strlen($input), 0, $params->getHost(), intval($params->getPort()));
        if (!$result) {
            $errorCode = socket_last_error();
            $errorMessage = socket_strerror($errorCode);
            throw new RequestException($this->getRRLang()->rrPointSentProblem($errorMessage), $errorCode);
        }
        return $this;
    }

    /**
     * @param resource $filePointer
     * @throws RequestException
     * @return $this
     */
    protected function readResponse($filePointer): parent
    {
        $this->remoteResponse = null;
        $reply = null;
        $result = socket_recv($filePointer, $reply, $this->bytesPerSegment(), MSG_WAITALL);
        if (false === $result) { // because could return size 0 bytes
            $errorCode = socket_last_error();
            $errorMessage = socket_strerror($errorCode);
            throw new RequestException($this->getRRLang()->rrPointReceivedProblem($errorMessage), $errorCode);
        }
        if (!is_null($reply)) {
            $response = Helper::getTempStorage();
            fwrite($response, $reply);
            rewind($response);
            $this->remoteResponse = $response;
        }
        return $this;
    }

    /**
     * How many bytes of loaded segment
     * @return int
     */
    protected function bytesPerSegment(): int
    {
        return 2045;
    }
}
