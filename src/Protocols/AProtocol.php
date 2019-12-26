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
    /** @var RemoteRequest\Wrappers\AWrapper */
    protected $target = null;
    /** @var Dummy\Query */
    protected $query = null;
    /** @var Dummy\Answer */
    protected $answer = null;

    public function __construct(array $contextOptions = [], bool $long = false)
    {
        $pointer = empty($contextParams)
            ? ($long ? new RemoteRequest\Pointers\Pfsocket() : new RemoteRequest\Pointers\Fsocket())
            : (new RemoteRequest\Pointers\Stream())->setContextOptions($contextOptions) ;
        $this->processor = new RemoteRequest\Connection\Processor($pointer);
        $this->target = $this->loadTarget();
        $this->query = $this->loadQuery();
        $this->answer = $this->loadAnswer();
    }

    abstract protected function loadTarget(): RemoteRequest\Wrappers\AWrapper;

    abstract protected function loadQuery(): Dummy\Query;

    abstract protected function loadAnswer(): Dummy\Answer;

    public function getTarget(): RemoteRequest\Wrappers\AWrapper
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
            && ($this->query instanceof RemoteRequest\Connection\ISettings)) {
            $this->target->setRequest($this->query);
        }

        $this->answer->setResponse(
            $this->processor
                ->setProtocolWrapper($this->target)
                ->setData($this->query)
                ->getResponse()
        );
        return $this->answer;
    }
}
