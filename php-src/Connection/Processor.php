<?php

namespace kalanis\RemoteRequest\Connection;


use kalanis\RemoteRequest\Interfaces;
use kalanis\RemoteRequest\Pointers;
use kalanis\RemoteRequest\RequestException;
use kalanis\RemoteRequest\Sockets;
use kalanis\RemoteRequest\Traits\TLang;


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
    use TLang;

    protected ?Interfaces\IQuery $data = null;
    protected Pointers\Processor $processor;
    protected ?Interfaces\IConnectionParams $params = null;
    protected Sockets\ASocket $socket;

    public function __construct(?Sockets\ASocket $method = null, ?Interfaces\IRRTranslations $lang = null)
    {
        $this->setRRLang($lang);
        $this->socket = $method ?: new Sockets\FSocket($lang);
        $this->processor = ($this->socket instanceof Sockets\Socket)
            ? new Pointers\SocketProcessor($lang)
            : new Pointers\Processor($lang)
        ;
    }

    /**
     * @param Interfaces\IConnectionParams $params
     * @return Processor
     * Necessary to set only once - when you say where you need to connect
     */
    public function setConnectionParams(Interfaces\IConnectionParams $params): self
    {
        $this->socket->close(); // close previously used connection
        $this->params = $params;
        return $this;
    }

    public function setData(?Interfaces\IQuery $request): self
    {
        $this->data = $request;
        return $this;
    }

    /**
     * Process query itself
     * @throws RequestException
     * @return $this
     * processPointer will make a new connection when the current one is not available
     */
    public function process(): self
    {
        if (!$this->params) {
            throw new RequestException($this->getRRLang()->rrPointUnknownTarget());
        }
        $this->processor
            ->setQuery($this->data)
            ->processPointer($this->socket->getResourcePointer($this->params), $this->params)
        ;
        return $this;
    }

    /**
     * What come back
     * @return resource|null
     */
    public function getResponse()
    {
        return $this->processor->getContent();
    }
}
