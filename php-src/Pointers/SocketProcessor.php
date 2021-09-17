<?php

namespace kalanis\RemoteRequest\Pointers;


use kalanis\RemoteRequest\RequestException;
use kalanis\RemoteRequest\Schemas\ASchema;


/**
 * Class SocketProcessor
 * @package kalanis\RemoteRequest\Pointers
 * Query to the remote server - read into provided output
 */
class SocketProcessor extends Processor
{
    /** @var int how many bytes for load split */
    const PART_SPLIT = 2045;

    /**
     * @param resource $filePointer
     * @param ASchema $wrapper
     * @return $this
     * @throws RequestException
     * @codeCoverageIgnore because accessing remote source via internal socket
     */
    protected function writeRequest($filePointer, ASchema $wrapper): parent
    {
        $input = $this->remoteQuery->getData();
        $result = socket_sendto($filePointer, $input, strlen($input), 0, $wrapper->getHost(), $wrapper->getPort());
        if (!$result) {
            $errorCode = socket_last_error();
            $errorMessage = socket_strerror($errorCode);
            throw new RequestException('Send problem: ' . $errorMessage, $errorCode);
        }
        return $this;
    }

    /**
     * @param resource $filePointer
     * @return $this
     * @throws RequestException
     * @codeCoverageIgnore because accessing remote source via internal socket
     */
    protected function readResponse($filePointer): parent
    {
        $reply = '';
        $result = socket_recv($filePointer, $reply , static::PART_SPLIT , MSG_WAITALL);
        if (false === $result) { // because could return size 0 bytes
            $errorCode = socket_last_error();
            $errorMessage = socket_strerror($errorCode);
            throw new RequestException('Receive problem: ' . $errorMessage, $errorCode);
        }
        $this->remoteResponse = $reply;
        return $this;
    }
}
