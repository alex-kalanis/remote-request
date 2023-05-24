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
    use RemoteRequest\Traits\TLang;

    /** @var RemoteRequest\Connection\Processor */
    protected $processor = null;
    /** @var RemoteRequest\Connection\Params\AParams */
    protected $params = null;
    /** @var Dummy\Query */
    protected $query = null;
    /** @var Dummy\Answer */
    protected $answer = null;

    /**
     * @param array<string, array<string, string>|string> $contextOptions
     * @param bool $long
     * @param RemoteRequest\Interfaces\IRRTranslations|null $lang
     */
    public function __construct(array $contextOptions = [], bool $long = false, ?RemoteRequest\Interfaces\IRRTranslations $lang = null)
    {
        $this->setRRLang($lang);
        $pointer = empty($contextOptions)
            ? ($long ? new RemoteRequest\Sockets\PfSocket($lang) : new RemoteRequest\Sockets\FSocket($lang))
            : (new RemoteRequest\Sockets\Stream($lang))->setContextOptions($contextOptions) ;
        $this->processor = new RemoteRequest\Connection\Processor($pointer, $lang);
        $this->params = $this->loadParams();
        $this->query = $this->loadQuery();
        $this->answer = $this->loadAnswer();
    }

    abstract protected function loadParams(): RemoteRequest\Connection\Params\AParams;

    abstract protected function loadQuery(): Dummy\Query;

    abstract protected function loadAnswer(): Dummy\Answer;

    public function getParams(): RemoteRequest\Connection\Params\AParams
    {
        return $this->params;
    }

    public function getQuery(): Dummy\Query
    {
        return $this->query;
    }

    /**
     * @throws RemoteRequest\RequestException
     * @return Dummy\Answer
     * @codeCoverageIgnore because it's about querying remote machine
     */
    public function getAnswer(): Dummy\Answer
    {
        $target = $this->query;
        if (empty($this->params->getHost())
            && ($target instanceof RemoteRequest\Interfaces\ITarget)) {
            $this->params->setTarget($target->getHost(), $target->getPort());
        }

        $this->answer->setResponse(
            $this->processor
                ->setConnectionParams($this->params)
                ->setData($this->query)
                ->process()
                ->getResponse()
        );
        return $this->answer;
    }
}
