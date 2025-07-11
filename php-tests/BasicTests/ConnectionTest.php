<?php

namespace tests\BasicTests;


use tests\CommonTestClass;
use tests\BasicTests\Connection as test;
use kalanis\RemoteRequest\Connection;
use kalanis\RemoteRequest\Protocols;
use kalanis\RemoteRequest\RequestException;
use kalanis\RemoteRequest\Sockets;


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

    /**
     * @throws RequestException
     */
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
        $params = new Connection\Params\Php();
        $params->setTarget(Connection\Params\Php::HOST_MEMORY);
        $processor = new test\ConnectProcessorMock(new Sockets\Socket());
        $processor->setConnectionParams($params);
        $processor->setData($query);
        $processor = new test\ConnectProcessorMock(new Sockets\SharedInternal());
        $processor->setConnectionParams($params);
        $processor->setData($query);
        $processor->process();
        $this->assertEquals(substr($content, 0, 2000), stream_get_contents($processor->getResponse()));
    }

    /**
     * @throws RequestException
     */
    public function testRepeatConnectionUse(): void
    {
        $content1 = str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz', 10);
        $content2 = str_repeat('ZYXWVUTSRQPONMLKJIHGFEDCBA9876543210zyxwvutsrqponmlkjihgfedcba', 10);
        $params = new Connection\Params\Php();
        $params->setTarget(Connection\Params\Php::HOST_MEMORY);
        $processor = new test\ConnectProcessorMock(new Sockets\SharedInternal());
        $processor->setConnectionParams($params);
        $query1 = new Protocols\Dummy\Query();
        $query1->body = $content1;
        $processor->setData($query1);
        $processor->process();
        $this->assertEquals($content1, stream_get_contents($processor->getResponse()));

        $query2 = new Protocols\Dummy\Query();
        $query2->body = $content2;
        $processor->setData($query2);
        $processor->process();
        $this->assertEquals($content2, stream_get_contents($processor->getResponse()));
    }

    /**
     * @throws RequestException
     */
    public function testNoConnectionParams(): void
    {
        $content1 = str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz', 10);
        $processor = new test\ConnectProcessorMock(new Sockets\SharedInternal());
        // no connection params set
        $query1 = new Protocols\Dummy\Query();
        $query1->body = $content1;
        $processor->setData($query1);
        $this->expectException(RequestException::class);
        $processor->process();
    }

    /**
     * @param string|null $message what to send to remote machine
     * @throws RequestException
     * @return string
     */
    protected function queryOnMock(?string $message): string
    {
        $params = new Connection\Params\Php();
        $params->setTarget(Connection\Params\Php::HOST_MEMORY);
        $processor = new test\ConnectProcessorMock(new Sockets\SharedInternal());
        $processor->setConnectionParams($params);
        if (!is_null($message)) {
            $query = new Protocols\Dummy\Query();
            $query->body = $message;
            $processor->setData($query);
        }
        $processor->process();
        $response = $processor->getResponse();
        return $response ? stream_get_contents($response) : '' ;
    }
}
