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
    public function getRemotePointer(Schemas\ASchema $protocolWrapper)
    {
        return null;
    }
}

class ExceptionTestSocket extends Sockets\ASocket
{
    public function getRemotePointer(Schemas\ASchema $protocolWrapper)
    {
        throw new RequestException('Cannot establish connection');
    }
}

class PointersTest extends CommonTestClass
{
    /**
     * When it blows
     * @expectedException \RemoteRequest\RequestException
     */
    public function testCallException()
    {
        $processor = new Connection\Processor(new ExceptionTestSocket());
        $processor->setProtocolSchema(new Schemas\File());
        $processor->setData(new Protocols\Dummy\Query());
        $processor->getResponse(); // die
    }

    /**
     * When it blows
     * @expectedException \RemoteRequest\RequestException
     */
    public function testCallNoPointer()
    {
        $processor = new Connection\Processor(new EmptyTestSocket());
        $processor->setProtocolSchema(new Schemas\File());
        $processor->setData(new Protocols\Dummy\Query());
        $processor->getResponse(); // die
    }
}