<?php

namespace RemoteRequest\Connection;

use RemoteRequest\Pointers;
use RemoteRequest\RequestException;
use RemoteRequest\Schemas\ASchema;
use RemoteRequest\Sockets;

/**
 * Query to the remote server - completing and simple processing
 * This is the network layer, what lies elsewhere and what has been sent through is not important for processing
 * It only needs to know destination, message to the destination and how to get response from remote; not how to make
 * something sane from response. That is not this one's responsibility.
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

    public function __construct(Sockets\ASocket $method = null)
    {
        $this->socket = (empty($method)) ? new Sockets\FSocket() : $method ;
        $this->processor = ($this->socket instanceof Sockets\Socket)
            ? new Pointers\SocketProcessor()
            : new Pointers\Processor()
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
     * @return string
     * @throws RequestException
     */
    public function getResponse(): string
    {
        return $this->processor
                ->setQuery($this->data)
                ->processPointer($this->socket->getResourcePointer($this->schema), $this->schema)
                ->getContent()
            ;
    }
}
