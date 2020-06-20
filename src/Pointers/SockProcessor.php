<?php

namespace RemoteRequest\Pointers;

use RemoteRequest;

/**
 * Query to the remote server - read into provided output
 */
class SockProcessor extends Processor
{
    /** @var int how many bytes for load split */
    const PART_SPLIT = 2045;

    /**
     * @param resource $filePointer
     * @param RemoteRequest\Wrappers\AWrapper $wrapper
     * @return $this
     * @throws RemoteRequest\RequestException
     * @codeCoverageIgnore because accessing remote source via internal socket
     */
    protected function writeRequest($filePointer, RemoteRequest\Wrappers\AWrapper $wrapper)
    {
        $input = $this->remoteQuery->getData();
        $result = socket_sendto($filePointer, $input , strlen($input) , 0 , $wrapper->getHost() , $wrapper->getPort());
        if (!$result) {
            $errorCode = socket_last_error();
            $errorMesssage = socket_strerror($errorCode);
            throw new RemoteRequest\RequestException('Send problem: ' . $errorMesssage, $errorCode);
        }
        return $this;
    }

    /**
     * @param resource $filePointer
     * @return $this
     * @throws RemoteRequest\RequestException
     * @codeCoverageIgnore because accessing remote source via internal socket
     */
    protected function readResponse($filePointer)
    {
        $reply = '';
        $result = socket_recv ( $filePointer, $reply , static::PART_SPLIT , MSG_WAITALL );
        if (false === $result) { // because could return size 0 bytes
            $errorCode = socket_last_error();
            $errorMesssage = socket_strerror($errorCode);
            throw new RemoteRequest\RequestException('Receive problem: ' . $errorMesssage, $errorCode);
        }
        $this->remoteResponse = $reply;
        return $this;
    }
}
