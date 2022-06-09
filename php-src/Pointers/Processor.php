<?php

namespace kalanis\RemoteRequest\Pointers;


use kalanis\RemoteRequest\Interfaces\IQuery;
use kalanis\RemoteRequest\Protocols\Helper;
use kalanis\RemoteRequest\RequestException;
use kalanis\RemoteRequest\Schemas\ASchema;


/**
 * Class Processor
 * @package kalanis\RemoteRequest\Pointers
 * Query to the remote server - read into provided output
 */
class Processor
{
    /** @var int how many bytes for load split */
    const PART_SPLIT = 1024;

    /** @var IQuery | null */
    protected $remoteQuery = null;
    /** @var resource|null */
    protected $remoteResponse = null;

    public function setQuery(?IQuery $content): self
    {
        $this->remoteQuery = $content;
        return $this;
    }

    /**
     * @param resource $filePointer
     * @param ASchema $wrapper
     * @return $this
     * @throws RequestException
     * @codeCoverageIgnore because accessing remote resources, similar code is in overwrite
     */
    public function processPointer($filePointer, ASchema $wrapper): self
    {
        $this->checkQuery();
        $this->checkPointer($filePointer);
        $this->writeRequest($filePointer, $wrapper);
        $this->readResponse($filePointer);
        return $this;
    }

    /**
     * @param resource $filePointer
     * @param ASchema $wrapper
     * @return $this
     */
    protected function writeRequest($filePointer, ASchema $wrapper): self
    {
        fwrite($filePointer, $this->remoteQuery->getData());
        return $this;
    }

    /**
     * @param resource $filePointer
     * @return $this
     */
    protected function readResponse($filePointer): self
    {
        $this->remoteResponse = null;

        // Read the server response
        $response = Helper::getTempStorage();
        $bytesLeft = $this->remoteQuery->getMaxAnswerLength();
        stream_copy_to_stream($filePointer, $response, (is_null($bytesLeft) ? -1 : $bytesLeft), 0);
        rewind($response);
        $this->remoteResponse = $response;
        return $this;
    }

    /**
     * @throws RequestException
     */
    protected function checkQuery(): void
    {
        if (empty($this->remoteQuery)
            || !($this->remoteQuery instanceof IQuery)) {
            throw new RequestException('Unknown target data for request');
        }
    }

    /**
     * @param resource|null $filePointer
     * @throws RequestException
     */
    protected function checkPointer($filePointer): void
    {
        if (empty($filePointer)
            || !is_resource($filePointer)) {
            throw new RequestException('No stream pointer defined');
        }
    }

    /**
     * @return resource|null
     */
    public function getContent()
    {
        return $this->remoteResponse;
    }
}
