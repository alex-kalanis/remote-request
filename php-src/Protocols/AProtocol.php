<?php

namespace kalanis\RemoteRequest\Protocols;


use kalanis\RemoteRequest;


/**
 * Class AProtocol
 * @package kalanis\RemoteRequest\Protocols
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
    /** @var RemoteRequest\Interfaces\IRRTranslations */
    protected $lang = null;

    public function __construct(RemoteRequest\Interfaces\IRRTranslations $lang, array $contextOptions = [], bool $long = false)
    {
        $this->lang = $lang;
        $pointer = empty($contextParams)
            ? ($long ? new RemoteRequest\Sockets\Pfsocket($lang) : new RemoteRequest\Sockets\Fsocket($lang))
            : (new RemoteRequest\Sockets\Stream($lang))->setContextOptions($contextOptions) ;
        $this->processor = new RemoteRequest\Connection\Processor($lang, $pointer);
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
     * @codeCoverageIgnore because it's about querying remote machine
     */
    public function getAnswer(): Dummy\Answer
    {
        $target = $this->query;
        if (empty($this->target->getHost())
            && ($target instanceof RemoteRequest\Interfaces\ITarget)) {
            $this->target->setRequest($target);
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
