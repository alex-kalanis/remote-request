<?php

namespace BasicTests;


use CommonTestClass;
use kalanis\RemoteRequest\Connection;
use kalanis\RemoteRequest\Interfaces;
use kalanis\RemoteRequest\Protocols;
use kalanis\RemoteRequest\RequestException;
use kalanis\RemoteRequest\Sockets;


class EmptyTestSocket extends Sockets\ASocket
{
    protected function remotePointer(Interfaces\IConnectionParams $schema)
    {
        return null;
    }
}


class ExceptionTestSocket extends Sockets\ASocket
{
    protected function remotePointer(Interfaces\IConnectionParams $schema)
    {
        throw new RequestException($this->getRRLang()->rrSocketCannotConnect());
    }
}


class PointersTest extends CommonTestClass
{
    /**
     * When it blows
     * @throws RequestException
     */
    public function testCallException(): void
    {
        $processor = new Connection\Processor(new ExceptionTestSocket());
        $processor->setConnectionParams(new Connection\Params\File());
        $processor->setData(new Protocols\Dummy\Query());
        $this->expectException(RequestException::class);
        $processor->process(); // die
    }

    /**
     * When it blows
     * @throws RequestException
     */
    public function testCallNoPointer(): void
    {
        $processor = new Connection\Processor(new EmptyTestSocket());
        $processor->setConnectionParams(new Connection\Params\File());
        $processor->setData(new Protocols\Dummy\Query());
        $this->expectException(RequestException::class);
        $processor->process(); // die
    }
}
