<?php

namespace BasicTests;


use CommonTestClass;
use kalanis\RemoteRequest\Connection;
use kalanis\RemoteRequest\Interfaces;
use kalanis\RemoteRequest\Pointers;
use kalanis\RemoteRequest\Protocols;
use kalanis\RemoteRequest\RequestException;
use kalanis\RemoteRequest\Schemas;
use kalanis\RemoteRequest\Sockets;
use kalanis\RemoteRequest\Translations;


class ConnectProcessorMock extends Connection\Processor
{
    public function __construct(Interfaces\IRRTranslations $lang, Sockets\ASocket $method = null)
    {
        parent::__construct($lang, $method);
        $this->processor = new PointerProcessorMock($lang);
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
        $lang = new Translations();
        $content = str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz', 100);
        $query = new Protocols\Dummy\Query();
        $query->body = $content;
        $query->maxLength = 2000;
        $wrapper = new Schemas\Php();
        $wrapper->setTarget(Schemas\Php::HOST_MEMORY);
        $processor = new ConnectProcessorMock($lang, new Sockets\Socket($lang));
        $processor->setProtocolSchema($wrapper);
        $processor->setData($query);
        $processor = new ConnectProcessorMock($lang, new Sockets\SharedInternal($lang));
        $processor->setProtocolSchema($wrapper);
        $processor->setData($query);
        $this->assertEquals(substr($content, 0, 2000), stream_get_contents($processor->getResponse()));
    }

    /**
     * @throws RequestException
     */
    public function testRepeatConnectionUse(): void
    {
        $lang = new Translations();
        $content1 = str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz', 10);
        $content2 = str_repeat('ZYXWVUTSRQPONMLKJIHGFEDCBA9876543210zyxwvutsrqponmlkjihgfedcba', 10);
        $wrapper = new Schemas\Php();
        $wrapper->setTarget(Schemas\Php::HOST_MEMORY);
        $processor = new ConnectProcessorMock($lang, new Sockets\SharedInternal($lang));
        $processor->setProtocolSchema($wrapper);
        $query1 = new Protocols\Dummy\Query();
        $query1->body = $content1;
        $processor->setData($query1);
        $this->assertEquals($content1, stream_get_contents($processor->getResponse()));

        $query2 = new Protocols\Dummy\Query();
        $query2->body = $content2;
        $processor->setData($query2);
        $this->assertEquals($content2, stream_get_contents($processor->getResponse()));
    }

    /**
     * @param string|null $message what to send to remote machine
     * @throws RequestException
     * @return string
     */
    protected function queryOnMock(?string $message): string
    {
        $lang = new Translations();
        $wrapper = new Schemas\Php();
        $wrapper->setTarget(Schemas\Php::HOST_MEMORY);
        $processor = new ConnectProcessorMock($lang, new Sockets\SharedInternal($lang));
        $processor->setProtocolSchema($wrapper);
        if (!is_null($message)) {
            $query = new Protocols\Dummy\Query();
            $query->body = $message;
            $processor->setData($query);
        }
        $response = $processor->getResponse();
        return $response ? stream_get_contents($response) : '' ;
    }
}
