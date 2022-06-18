<?php

namespace ProtocolsTests\Http;


use CommonTestClass;
use kalanis\RemoteRequest\Connection;
use kalanis\RemoteRequest\Protocols\Dummy;
use kalanis\RemoteRequest\Protocols\Http;
use kalanis\RemoteRequest\RequestException;
use kalanis\RemoteRequest\Schemas;
use kalanis\RemoteRequest\Translations;


class TestProcessor extends Connection\Processor
{
    public function getResponse()
    {
        return CommonTestClass::stringToResource('HTTP/0.1 900 KO' . Http::DELIMITER);
    }
}


class ContentTestProcessor extends TestProcessor
{
    public function getResponse()
    {
        return CommonTestClass::stringToResource('HTTP/0.1 901 KO' . Http::DELIMITER . Http::DELIMITER . 'abcdefghijkl');
    }
}


class SimpleQueryTest extends CommonTestClass
{
    /**
     * When the answer is empty
     * @throws RequestException
     */
    public function testSetsSimple(): void
    {
        $result = $this->queryOnMock(new TestProcessor(new Translations()));
        $this->assertEquals(900, $result->getCode());
        $this->assertEquals('', $result->getContent());
    }

    /**
     * When the answer contains something
     * @throws RequestException
     */
    public function testSetsBody(): void
    {
        $result = $this->queryOnMock(new ContentTestProcessor(new Translations()));
        $this->assertEquals(901, $result->getCode());
        $this->assertEquals('abcdefghijkl', $result->getContent());
    }

    /**
     * @param Connection\Processor $processor cim vrati vzdalena data
     * @throws RequestException
     * @return Http\Answer
     */
    protected function queryOnMock(Connection\Processor $processor): Http\Answer
    {
        $processor->setData(new Dummy\Query());
        $processor->setProtocolSchema(new Schemas\Tcp());
        $answer = new Http\Answer(new Translations());
        return $answer->setResponse($processor->getResponse());
    }
}
