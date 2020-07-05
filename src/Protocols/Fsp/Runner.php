<?php

namespace RemoteRequest\Protocols\Fsp;

use RemoteRequest;
use RemoteRequest\Schemas;

/**
 * Runner for FSP under RemoteRequest
 */
class Runner
{
    /** @var Schemas\ASchema */
    protected $schema = null;
    /** @var RemoteRequest\Connection\Processor */
    protected $processor = null;
    /** @var Query */
    protected $query = null;
    /** @var Answer */
    protected $answer = null;
    /** @var Session */
    protected $session = null;
    /** @var Query\AQuery|null */
    protected $actionQuery = null;

    /**
     * @throws RemoteRequest\RequestException
     */
    public function __construct()
    {
        $this->schema = Schemas\ASchema::getSchema(Schemas\ASchema::SCHEMA_UDP);
        $this->processor = new RemoteRequest\Connection\Processor(new RemoteRequest\Sockets\Socket());
        $this->query = new Query();
        $this->answer = new Answer();
        $this->session = new Session();
    }

    /**
     * @throws RemoteRequest\RequestException
     */
    public function __destruct()
    {
        if ($this->session->hasKey()) {
            $this->sendBye();
        }
    }

    public function setActionQuery(Query\AQuery $query): self
    {
        $this->actionQuery = $query;
        return $this;
    }

    public function getQuery(): Query
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
     * @return Answer\AAnswer
     * @throws RemoteRequest\RequestException
     */
    public function process(): Answer\AAnswer
    {
        if (empty($this->actionQuery)) {
            throw new RemoteRequest\RequestException('No action set.');
        }
        $this->session->setHost($this->schema->getHost());
        $this->actionQuery
            ->setKey($this->session->getKey())
            ->setSequence($this->session->getSequence())
            ->compile()
        ;
        $response = $this->processor->setProtocolSchema($this->schema)->setData($this->query)->getResponse();
        $answer = Answer\AnswerFactory::getObject(
            $this->answer->setResponse(
                $response
            )->process()
        );
        if ($answer instanceof Answer\Error) {
            throw $answer->getError();
        }
        $this->session
            ->setKey($answer->getDataClass()->getKey())
            ->updateSequence($answer->getDataClass()->getSequence())
        ;
        return $answer;
    }

    /**
     * @throws RemoteRequest\RequestException
     */
    protected function sendBye(): void
    {
        $answer = $this->setActionQuery(new Query\Bye($this->getQuery()))
            ->process()
        ;
        /** @var Answer\Nothing $answer */
        if (!$answer instanceof Answer\Nothing) {
            throw new RemoteRequest\RequestException('Got something bad with close. Class ' . get_class($answer));
        }
        $this->session->clear();
    }
}
