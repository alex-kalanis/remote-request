<?php

namespace kalanis\RemoteRequest\Pointers;


use kalanis\RemoteRequest\Interfaces\IConnectionParams;
use kalanis\RemoteRequest\Interfaces\IQuery;
use kalanis\RemoteRequest\Interfaces\IRRTranslations;
use kalanis\RemoteRequest\Protocols\Helper;
use kalanis\RemoteRequest\RequestException;
use kalanis\RemoteRequest\Traits\TLang;


/**
 * Class Processor
 * @package kalanis\RemoteRequest\Pointers
 * Query to the remote server - read into provided output
 */
class Processor
{
    use TLang;

    /** @var IQuery|null */
    protected $remoteQuery = null;
    /** @var resource|null */
    protected $remoteResponse = null;

    public function __construct(?IRRTranslations $lang = null)
    {
        $this->setRRLang($lang);
    }

    public function setQuery(?IQuery $content): self
    {
        $this->remoteQuery = $content;
        return $this;
    }

    /**
     * @param resource|null $filePointer
     * @param IConnectionParams $params
     * @throws RequestException
     * @return $this
     * @codeCoverageIgnore because accessing remote resources, similar code is in overwrite
     */
    public function processPointer($filePointer, IConnectionParams $params): self
    {
        $this->checkPointer($filePointer);
        $this->writeRequest($filePointer, $params); // @phpstan-ignore-line
        $this->readResponse($filePointer); // @phpstan-ignore-line
        return $this;
    }

    /**
     * @param resource $filePointer
     * @param IConnectionParams $params
     * @throws RequestException
     * @return $this
     */
    protected function writeRequest($filePointer, IConnectionParams $params): self
    {
        $srcStream = $this->getQuery()->getData(); // always exists - checked
        rewind($srcStream);
        stream_copy_to_stream($srcStream, $filePointer);
        return $this;
    }

    /**
     * @param resource $filePointer
     * @throws RequestException
     * @return $this
     */
    protected function readResponse($filePointer): self
    {
        $this->remoteResponse = null;

        // Read the server response
        $response = Helper::getTempStorage();
        $bytesLeft = $this->getQuery()->getMaxAnswerLength(); // always exists - checked
        stream_copy_to_stream($filePointer, $response, (is_null($bytesLeft) ? -1 : $bytesLeft), 0);
        rewind($response);
        $this->remoteResponse = $response;
        return $this;
    }

    /**
     * @throws RequestException
     * @return IQuery
     */
    protected function getQuery(): IQuery
    {
        if (empty($this->remoteQuery)
            || !($this->remoteQuery instanceof IQuery)) {
            throw new RequestException($this->getRRLang()->rrPointUnknownTarget());
        }
        return $this->remoteQuery;
    }

    /**
     * @param resource|null $filePointer
     * @throws RequestException
     */
    protected function checkPointer($filePointer): void
    {
        if (empty($filePointer)
            || !is_resource($filePointer)) {
            throw new RequestException($this->getRRLang()->rrPointNoStreamPointer());
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
