<?php

namespace BasicTests;


use CommonTestClass;
use RemoteRequest\Connection;
use RemoteRequest\Protocols;
use RemoteRequest\RequestException;
use RemoteRequest\Schemas;
use RemoteRequest\Sockets;


class EmptyTestSocket extends Sockets\ASocket
{
    protected function remotePointer(Schemas\ASchema $protocolWrapper)
    {
        return null;
    }
}


class ExceptionTestSocket extends Sockets\ASocket
{
    protected function remotePointer(Schemas\ASchema $protocolWrapper)
    {
        throw new RequestException('Cannot establish connection');
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
        $processor->setProtocolSchema(new Schemas\File());
        $processor->setData(new Protocols\Dummy\Query());
        $this->expectException(RequestException::class);
        $processor->getResponse(); // die
    }

    /**
     * When it blows
     * @throws RequestException
     */
    public function testCallNoPointer(): void
    {
        $processor = new Connection\Processor(new EmptyTestSocket());
        $processor->setProtocolSchema(new Schemas\File());
        $processor->setData(new Protocols\Dummy\Query());
        $this->expectException(RequestException::class);
        $processor->getResponse(); // die
    }
}