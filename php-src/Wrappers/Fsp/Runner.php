<?php

namespace kalanis\RemoteRequest\Wrappers\Fsp;


use kalanis\RemoteRequest\Connection;
use kalanis\RemoteRequest\Interfaces\IRRTranslations;
use kalanis\RemoteRequest\Interfaces\ISchema;
use kalanis\RemoteRequest\Protocols\Fsp as Protocol;
use kalanis\RemoteRequest\RequestException;
use kalanis\RemoteRequest\Sockets;


/**
 * Class Runner
 * @package kalanis\RemoteRequest\Wrappers\Fsp
 * Runner for FSP under RemoteRequest
 */
class Runner
{
    /** @var IRRTranslations */
    protected $lang = null;
    /** @var Connection\Params\AParams */
    protected $params = null;
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
    public function __construct(IRRTranslations $lang)
    {
        $this->lang = $lang;
        $this->params = Connection\Params\Factory::getForSchema($lang, ISchema::SCHEMA_UDP);
        $this->processor = new Connection\Processor($lang, new Sockets\Socket($lang));
        $this->query = new Protocol\Query();
        $this->answer = new Protocol\Answer($lang);
        $this->session = new Protocol\Session($lang);
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

    public function getConnectParams(): Connection\Params\AParams
    {
        return $this->params;
    }

    public function getTimeout(/** @scrutinizer ignore-unused */ string $host): int
    {
        return 10;
        // this one will be based on info from server session
    }

    /**
     * Process queries to remote machine
     * @throws RequestException
     * @return Protocol\Answer\AAnswer
     */
    public function process(): Protocol\Answer\AAnswer
    {
        if (empty($this->actionQuery)) {
            throw new RequestException($this->lang->rrFspNoAction());
        }
        if (empty($this->params->getHost())) {
            throw new RequestException($this->lang->rrFspNoTarget());
        }
        $this->session->setHost($this->params->getHost());
        $this->actionQuery
            ->setKey($this->session->getKey())
            ->setSequence($this->session->getSequence())
            ->compile()
        ;
        $response = $this->processor->setConnectionParams($this->params)->setData($this->query)->process()->getResponse();
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
        if (!$answer instanceof Protocol\Answer\Nothing) {
            throw new RequestException($this->lang->rrFspBadResponseClose(get_class($answer)));
        }
        $this->session->clear();
    }
}
