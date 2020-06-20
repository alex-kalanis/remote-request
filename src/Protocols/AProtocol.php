<?php

namespace RemoteRequest\Protocols;

use RemoteRequest;

/**
 * Properties for query to remote server - raw UDP
 */
abstract class AProtocol
{
    /** @var RemoteRequest\Connection\Processor */
    protected $processor = null;
    /** @var RemoteRequest\Schemas\ASchema */
    protected $target = null;
    /** @var Dummy\Query */
    protected $query = null;
    /** @var Dummy\Answer */
    protected $answer = null;

    public function __construct(array $contextOptions = [], bool $long = false)
    {
        $pointer = empty($contextParams)
            ? ($long ? new RemoteRequest\Sockets\Pfsocket() : new RemoteRequest\Sockets\Fsocket())
            : (new RemoteRequest\Sockets\Stream())->setContextOptions($contextOptions) ;
        $this->processor = new RemoteRequest\Connection\Processor($pointer);
        $this->target = $this->loadTarget();
        $this->query = $this->loadQuery();
        $this->answer = $this->loadAnswer();
    }

    abstract protected function loadTarget(): RemoteRequest\Schemas\ASchema;

    abstract protected function loadQuery(): Dummy\Query;

    abstract protected function loadAnswer(): Dummy\Answer;

    public function getTarget(): RemoteRequest\Schemas\ASchema
    {
        return $this->target;
    }

    public function getQuery(): Dummy\Query
    {
        return $this->query;
    }

    /**
     * @return Dummy\Answer
     * @throws RemoteRequest\RequestException
     */
    public function getAnswer(): Dummy\Answer
    {
        if (empty($this->target->getHost())
            && ($this->query instanceof RemoteRequest\Connection\ITarget)) {
            $this->target->setRequest($this->query);
        }

        $this->answer->setResponse(
            $this->processor
                ->setProtocolSchema($this->target)
                ->setData($this->query)
                ->getResponse()
        );
        return $this->answer;
    }
}
