<?php

namespace ProtocolsTests\Http;

use CommonTestClass;
use RemoteRequest\Connection;
use RemoteRequest\Protocols\Dummy;
use RemoteRequest\Protocols\Http;
use RemoteRequest\RequestException;
use RemoteRequest\Schemas;

class TestProcessor extends Connection\Processor
{
    public function getResponse(): string
    {
        return 'HTTP/0.1 900 KO' . Http::DELIMITER;
    }
}

class ContentTestProcessor extends TestProcessor
{
    public function getResponse(): string
    {
        return 'HTTP/0.1 901 KO' . Http::DELIMITER . Http::DELIMITER . 'abcdefghijkl';
    }
}

class SimpleQueryTest extends CommonTestClass
{
    /**
     * When the answer is empty
     * @throws RequestException
     */
    public function testSetsSimple()
    {
        $result = $this->queryOnMock(new TestProcessor());
        $this->assertEquals(900, $result->getCode());
        $this->assertEquals('', $result->getContent());
    }

    /**
     * When the answer contains something
     * @throws RequestException
     */
    public function testSetsBody()
    {
        $result = $this->queryOnMock(new ContentTestProcessor());
        $this->assertEquals(901, $result->getCode());
        $this->assertEquals('abcdefghijkl', $result->getContent());
    }

    /**
     * @param Connection\Processor $processor cim vrati vzdalena data
     * @return Http\Answer
     * @throws RequestException
     */
    protected function queryOnMock(Connection\Processor $processor)
    {
        $processor->setData(new Dummy\Query());
        $processor->setProtocolSchema(new Schemas\Tcp());
        $answer = new Http\Answer();
        return $answer->setResponse($processor->getResponse());
    }

}