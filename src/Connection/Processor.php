<?php

namespace RemoteRequest\Connection;

use RemoteRequest;

/**
 * Query to the remote server - completing and simple processing
 * This is the network layer, what lies elsewhere and what has been sent through is not important for processing
 * It only needs to know destination, message to the destination and how to get response from remote; not how to make
 * something sane from response. That is not this one's responsibility.
 */
class Processor
{
    /** @var RemoteRequest\Pointers\ASocket */
    protected $socketPointer = null;
    /** @var RemoteRequest\Pointers\Processor */
    protected $processor = null;
    /** @var RemoteRequest\Wrappers\AWrapper */
    protected $wrapper = null;
    /** @var RemoteRequest\Connection\IQuery|null */
    protected $data = null;

    public function __construct(RemoteRequest\Pointers\ASocket $method = null)
    {
        $this->socketPointer = (empty($method)) ? new RemoteRequest\Pointers\Fsocket() : $method ;
        $this->processor = ($this->socketPointer instanceof RemoteRequest\Pointers\Socket)
            ? new RemoteRequest\Pointers\SockProcessor()
            : new RemoteRequest\Pointers\Processor()
        ;
    }

    public function setProtocolWrapper(RemoteRequest\Wrappers\AWrapper $wrapper)
    {
        $this->wrapper = $wrapper;
        return $this;
    }

    public function setData(?RemoteRequest\Connection\IQuery $request)
    {
        $this->data = $request;
        return $this;
    }

    /**
     * Process query itself
     * @return string
     * @throws RemoteRequest\RequestException
     */
    public function getResponse(): string
    {
        return $this->processor
                ->setQuery($this->data)
                ->processPointer($this->socketPointer->getRemotePointer($this->wrapper), $this->wrapper)
                ->getContent()
            ;
    }
}
