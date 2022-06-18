<?php

namespace kalanis\RemoteRequest\Connection;


use kalanis\RemoteRequest\Interfaces\IQuery;
use kalanis\RemoteRequest\Interfaces\IRRTranslations;
use kalanis\RemoteRequest\Pointers;
use kalanis\RemoteRequest\RequestException;
use kalanis\RemoteRequest\Schemas\ASchema;
use kalanis\RemoteRequest\Sockets;


/**
 * Class Processor
 * @package kalanis\RemoteRequest\Connection
 * Query to the remote server - completing and simple processing
 * This is the network layer, what lies elsewhere and what has been sent through is not important for processing
 * It only needs to know destination, message to the destination and how to get response from remote; not how to make
 * something sane from response. That is not responsibility of these classes.
 */
class Processor
{
    /** @var IQuery|null */
    protected $data = null;
    /** @var Pointers\Processor */
    protected $processor = null;
    /** @var ASchema */
    protected $schema = null;
    /** @var Sockets\ASocket */
    protected $socket = null;

    public function __construct(IRRTranslations $lang, Sockets\ASocket $method = null)
    {
        $this->socket = (empty($method)) ? new Sockets\FSocket($lang) : $method ;
        $this->processor = ($this->socket instanceof Sockets\Socket)
            ? new Pointers\SocketProcessor($lang)
            : new Pointers\Processor($lang)
        ;
    }

    public function setProtocolSchema(ASchema $wrapper): self
    {
        $this->schema = $wrapper;
        return $this;
    }

    public function setData(?IQuery $request): self
    {
        $this->data = $request;
        return $this;
    }

    /**
     * Process query itself
     * @throws RequestException
     * @return resource|null
     */
    public function getResponse()
    {
        return $this->processor
                ->setQuery($this->data)
                ->processPointer($this->socket->getResourcePointer($this->schema), $this->schema)
                ->getContent()
            ;
    }
}
