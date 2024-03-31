<?php

namespace kalanis\RemoteRequest\Wrappers\Fsp;


use kalanis\RemoteRequest\Connection;
use kalanis\RemoteRequest\Interfaces\IRRTranslations;
use kalanis\RemoteRequest\Interfaces\ISchema;
use kalanis\RemoteRequest\Protocols\Fsp as Protocol;
use kalanis\RemoteRequest\RequestException;
use kalanis\RemoteRequest\Sockets;
use kalanis\RemoteRequest\Traits\TLang;


/**
 * Class Runner
 * @package kalanis\RemoteRequest\Wrappers\Fsp
 * Runner for FSP under RemoteRequest
 */
class Runner
{
    use TLang;

    protected Connection\Params\AParams $params;
    protected Connection\Processor $processor;
    protected Protocol\Query $query;
    protected Protocol\Answer $answer;
    protected Protocol\Session $session;
    protected ?Protocol\Query\AQuery $actionQuery = null;

    /**
     * @param IRRTranslations $lang
     * @throws RequestException
     */
    public function __construct(IRRTranslations $lang)
    {
        $this->setRRLang($lang);
        $this->params = Connection\Params\Factory::getForSchema(ISchema::SCHEMA_UDP, $lang);
        $this->processor = new Connection\Processor(new Sockets\Socket($lang), $lang);
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

    /**
     * @throws RequestException
     * @return Protocol\Query\AQuery
     */
    protected function getActionQuery(): Protocol\Query\AQuery
    {
        if (!$this->actionQuery) {
            throw new RequestException($this->getRRLang()->rrFspNoAction());
        }
        return $this->actionQuery;
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
        if (empty($this->params->getHost())) {
            throw new RequestException($this->getRRLang()->rrFspNoTarget());
        }
        $this->session->setHost($this->params->getHost());
        $this->getActionQuery()
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
            throw new RequestException($this->getRRLang()->rrFspBadResponseClose(get_class($answer)));
        }
        $this->session->clear();
    }
}
