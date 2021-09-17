<?php

namespace kalanis\RemoteRequest\Wrappers\Fsp;


use kalanis\RemoteRequest\Connection;
use kalanis\RemoteRequest\Protocols\Fsp as Protocol;
use kalanis\RemoteRequest\RequestException;
use kalanis\RemoteRequest\Schemas;
use kalanis\RemoteRequest\Sockets;


/**
 * Class Runner
 * @package kalanis\RemoteRequest\Wrappers\Fsp
 * Runner for FSP under RemoteRequest
 */
class Runner
{
    /** @var Schemas\ASchema */
    protected $schema = null;
    /** @var Connection\Processor */
    protected $processor = null;
    /** @var Protocol\Query */
    protected $query = null;
    /** @var Protocol\Answer */
    protected $answer = null;
    /** @var Protocol\Session */
    protected $session = null;
    /** @var Protocol\Query\AQuery|null */
    protected $actionQuery = null;

    /**
     * @throws RequestException
     */
    public function __construct()
    {
        $this->schema = Schemas\Factory::getSchema(Schemas\ASchema::SCHEMA_UDP);
        $this->processor = new Connection\Processor(new Sockets\Socket());
        $this->query = new Protocol\Query();
        $this->answer = new Protocol\Answer();
        $this->session = new Protocol\Session();
    }

    /**
     * @throws RequestException
     */
    public function __destruct()
    {
        if ($this->session->hasKey()) {
            $this->sendBye();
        }
    }

    public function setActionQuery(Protocol\Query\AQuery $query): self
    {
        $this->actionQuery = $query;
        return $this;
    }

    public function getQuery(): Protocol\Query
    {
        return $this->query;
    }

    public function getSchema(): Schemas\ASchema
    {
        return $this->schema;
    }

    public function getTimeout(string $host): int
    {
        return 10;
        // this one will be based on info from server session
    }

    /**
     * Process queries to remote machine
     * @return Protocol\Answer\AAnswer
     * @throws RequestException
     */
    public function process(): Protocol\Answer\AAnswer
    {
        if (empty($this->actionQuery)) {
            throw new RequestException('No action set.');
        }
        if (empty($this->schema->getHost())) {
            throw new RequestException('No target.');
        }
        $this->session->setHost($this->schema->getHost());
        $this->actionQuery
            ->setKey($this->session->getKey())
            ->setSequence($this->session->getSequence())
            ->compile()
        ;
        $response = $this->processor->setProtocolSchema($this->schema)->setData($this->query)->getResponse();
        $answer = Protocol\Answer\AnswerFactory::getObject(
            $this->answer->setResponse(
                $response
            )->process()
        );
        if ($answer instanceof Protocol\Answer\Error) {
            throw $answer->getError();
        }
        $this->session
            ->setKey($answer->getDataClass()->getKey())
            ->updateSequence($answer->getDataClass()->getSequence())
        ;
        return $answer;
    }

    /**
     * @throws RequestException
     */
    protected function sendBye(): void
    {
        $answer = $this->setActionQuery(new Protocol\Query\Bye($this->getQuery()))
            ->process()
        ;
        /** @var Protocol\Answer\Nothing $answer */
        if (!$answer instanceof Protocol\Answer\Nothing) {
            throw new RequestException('Got something bad with close. Class ' . get_class($answer));
        }
        $this->session->clear();
    }
}
