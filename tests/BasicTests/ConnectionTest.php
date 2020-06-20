<?php

namespace BasicTests;

use CommonTestClass;
use RemoteRequest\Connection;
use RemoteRequest\Pointers;
use RemoteRequest\Protocols;
use RemoteRequest\RequestException;
use RemoteRequest\Schemas;
use RemoteRequest\Sockets;

class ConnectProcessorMock extends Connection\Processor
{
    public function __construct(Sockets\ASocket $method = null)
    {
        parent::__construct($method);
        $this->processor = new PointerProcessorMock();
    }
}

class PointerProcessorMock extends Pointers\Processor
{
    public function processPointer($filePointer, Schemas\ASchema $wrapper)
    {
        $this->checkQuery();
        $this->checkPointer($filePointer);
        $this->writeRequest($filePointer, $wrapper);
        rewind($filePointer); // FOR REASON
        $this->readResponse($filePointer);
        return $this;
    }
}

class ConnectionTest extends CommonTestClass
{
    /**
     * When it runs
     * The response is in query init
     * @throws RequestException
     */
    public function testSetsSimple()
    {
        $this->assertEquals('', $this->queryOnMock(''));
        $this->assertEquals('abcdefghijkl', $this->queryOnMock('abcdefghijkl'));
        $this->assertEquals('Hello.', $this->queryOnMock('Hello.'));
    }

    /**
     * @expectedException \RemoteRequest\RequestException
     */
    public function testSetsNoSocket()
    {
        $this->queryOnMock(null);
    }

    public function testSetsLongData()
    {
        $content = str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz', 200);
        $this->assertEquals($content, $this->queryOnMock($content));
    }

    /**
     * @throws RequestException
     */
    public function testSetsLongDataCut()
    {
        $content = str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz', 100);
        $query = new Protocols\Dummy\Query();
        $query->body = $content;
        $query->maxLength = 2000;
        $wrapper = new Schemas\Php();
        $wrapper->setTarget(Schemas\Php::HOST_MEMORY);
        $processor = new ConnectProcessorMock(new Sockets\Socket());
        $processor->setProtocolSchema($wrapper);
        $processor->setData($query);
        $processor = new ConnectProcessorMock(new Sockets\SharedInternal());
        $processor->setProtocolSchema($wrapper);
        $processor->setData($query);
        $this->assertEquals(substr($content, 0, 2000), $processor->getResponse());
    }

    /**
     * @param string|null $message what to send to remote machine
     * @return string
     * @throws RequestException
     */
    protected function queryOnMock(?string $message)
    {
        $wrapper = new Schemas\Php();
        $wrapper->setTarget(Schemas\Php::HOST_MEMORY);
        $processor = new ConnectProcessorMock(new Sockets\SharedInternal());
        $processor->setProtocolSchema($wrapper);
        if (!is_null($message)) {
            $query = new Protocols\Dummy\Query();
            $query->body = $message;
            $processor->setData($query);
        }
        return $processor->getResponse();
    }
}