<?php

namespace RemoteRequest\Pointers;


use RemoteRequest\Connection\IQuery;
use RemoteRequest\RequestException;
use RemoteRequest\Schemas\ASchema;


/**
 * Class Processor
 * @package RemoteRequest\Pointers
 * Query to the remote server - read into provided output
 */
class Processor
{
    /** @var int how many bytes for load split */
    const PART_SPLIT = 1024;

    /** @var IQuery | null */
    protected $remoteQuery = null;
    /** @var string */
    protected $remoteResponse = '';

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

    public function getContent(): string
    {
        return $this->remoteResponse;
    }
}
