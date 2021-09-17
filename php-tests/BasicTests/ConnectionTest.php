<?php

namespace BasicTests;


use CommonTestClass;
use kalanis\RemoteRequest\Connection;
use kalanis\RemoteRequest\Pointers;
use kalanis\RemoteRequest\Protocols;
use kalanis\RemoteRequest\RequestException;
use kalanis\RemoteRequest\Schemas;
use kalanis\RemoteRequest\Sockets;


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
    public function processPointer($filePointer, Schemas\ASchema $wrapper): parent
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
    public function testSetsSimple(): void
    {
        $this->assertEquals('', $this->queryOnMock(''));
        $this->assertEquals('abcdefghijkl', $this->queryOnMock('abcdefghijkl'));
        $this->assertEquals('Hello.', $this->queryOnMock('Hello.'));
    }

    /**
     * @throws RequestException
     */
    public function testSetsNoSocket(): void
    {
        $this->expectException(RequestException::class);
        $this->queryOnMock(null);
    }

    public function testSetsLongData(): void
    {
        $content = str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz', 200);
        $this->assertEquals($content, $this->queryOnMock($content));
    }

    /**
     * @throws RequestException
     */
    public function testSetsLongDataCut(): void
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
     * @throws RequestException
     */
    public function testRepeatConnectionUse(): void
    {
        $content1 = str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz', 10);
        $content2 = str_repeat('ZYXWVUTSRQPONMLKJIHGFEDCBA9876543210zyxwvutsrqponmlkjihgfedcba', 10);
        $wrapper = new Schemas\Php();
        $wrapper->setTarget(Schemas\Php::HOST_MEMORY);
        $processor = new ConnectProcessorMock(new Sockets\SharedInternal());
        $processor->setProtocolSchema($wrapper);
        $query1 = new Protocols\Dummy\Query();
        $query1->body = $content1;
        $processor->setData($query1);
        $this->assertEquals($content1, $processor->getResponse());

        $query2 = new Protocols\Dummy\Query();
        $query2->body = $content2;
        $processor->setData($query2);
        $this->assertEquals($content2, $processor->getResponse());
    }

    /**
     * @param string|null $message what to send to remote machine
     * @return string
     * @throws RequestException
     */
    protected function queryOnMock(?string $message): string
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
