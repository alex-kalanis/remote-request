<?php

namespace kalanis\RemoteRequest\Pointers;


use kalanis\RemoteRequest\Protocols\Helper;
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
     * @throws RequestException
     * @return $this
     * @codeCoverageIgnore because accessing remote source via internal socket
     */
    protected function writeRequest($filePointer, ASchema $wrapper): parent
    {
        $input = $this->remoteQuery ? strval(stream_get_contents($this->remoteQuery->getData(), -1, 0)) : '';
        $result = socket_sendto($filePointer, $input, strlen($input), 0, $wrapper->getHost(), $wrapper->getPort());
        if (!$result) {
            $errorCode = socket_last_error();
            $errorMessage = socket_strerror($errorCode);
            throw new RequestException($this->lang->rrPointSentProblem($errorMessage), $errorCode);
        }
        return $this;
    }

    /**
     * @param resource $filePointer
     * @throws RequestException
     * @return $this
     * @codeCoverageIgnore because accessing remote source via internal socket
     */
    protected function readResponse($filePointer): parent
    {
        $this->remoteResponse = null;
        $reply = null;
        $result = socket_recv($filePointer, $reply , static::PART_SPLIT , MSG_WAITALL);
        if (false === $result) { // because could return size 0 bytes
            $errorCode = socket_last_error();
            $errorMessage = socket_strerror($errorCode);
            throw new RequestException($this->lang->rrPointReceivedProblem($errorMessage), $errorCode);
        }
        if (!is_null($reply)) {
            $response = Helper::getTempStorage();
            fwrite($response, $reply);
            rewind($response);
            $this->remoteResponse = $response;
        }
        return $this;
    }
}
