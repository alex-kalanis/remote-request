<?php

namespace RemoteRequest\Pointers;

use RemoteRequest;

/**
 * Query to the remote server - read into provided output
 */
class Processor
{
    /** @var int how many bytes for load split */
    const PART_SPLIT = 1024;

    /** @var RemoteRequest\Connection\IQuery | null */
    protected $remoteQuery = null;
    /** @var string */
    protected $remoteResponse = '';

    public function setQuery(RemoteRequest\Connection\IQuery $content)
    {
        $this->remoteQuery = $content;
        return $this;
    }

    /**
     * @param $filePointer
     * @return $this
     * @throws RemoteRequest\RequestException
     */
    public function processPointer($filePointer)
    {
        $this->checkQuery();
        $this->checkPointer($filePointer);
        $this->writeRequest($filePointer);
        $this->readResponse($filePointer);
        return $this;
    }

    /**
     * @param resource $filePointer
     * @return $this
     */
    protected function writeRequest($filePointer)
    {
        fwrite($filePointer, $this->remoteQuery->getData());
        return $this;
    }

    /**
     * @param resource $filePointer
     * @return $this
     */
    protected function readResponse($filePointer)
    {
        $this->remoteResponse = '';

        // Read the server response
        $response = "";
        $bytesLeft = $this->remoteQuery->getMaxAnswerLength();

        while (!feof($filePointer)) {
            $nextCount = (is_null($bytesLeft)) ? static::PART_SPLIT : min(static::PART_SPLIT, $bytesLeft);

            if ($nextCount) {
                $line = fread($filePointer, $nextCount);
                $nextCount = mb_strlen($line);
                $response .= $line;
            }

            if (!is_null($bytesLeft)) {
                if ($bytesLeft <= $nextCount) {
                    break;
                }
                $bytesLeft -= $nextCount;
            }
        }

        fclose($filePointer);
        $this->remoteResponse = $response;
        return $this;
    }

    /**
     * @throws RemoteRequest\RequestException
     */
    protected function checkQuery(): void
    {
        if (empty($this->remoteQuery)
            || !($this->remoteQuery instanceof RemoteRequest\Connection\IQuery)) {
            throw new RemoteRequest\RequestException('Unknown target data for request');
        }
    }

    /**
     * @param resource|null $filePointer
     * @throws RemoteRequest\RequestException
     */
    protected function checkPointer($filePointer): void
    {
        if (empty($filePointer)
            || !is_resource($filePointer)) {
            throw new RemoteRequest\RequestException('No stream pointer defined');
        }
    }

    public function getContent(): string
    {
        return $this->remoteResponse;
    }
}
