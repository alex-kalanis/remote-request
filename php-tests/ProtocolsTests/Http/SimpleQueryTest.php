<?php

namespace tests\ProtocolsTests\Http;


use tests\CommonTestClass;
use kalanis\RemoteRequest\Connection;
use kalanis\RemoteRequest\Protocols\Dummy;
use kalanis\RemoteRequest\Protocols\Http;
use kalanis\RemoteRequest\RequestException;


class SimpleQueryTest extends CommonTestClass
{
    /**
     * When the answer is empty
     * @throws RequestException
     */
    public function testSetsSimple(): void
    {
        $result = $this->queryOnMock(new SimpleQuery\TestProcessor());
        $this->assertEquals(900, $result->getCode());
        $this->assertEquals('', $result->getContent());
    }

    /**
     * When the answer contains something
     * @throws RequestException
     */
    public function testSetsBody(): void
    {
        $result = $this->queryOnMock(new SimpleQuery\ContentTestProcessor());
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
        $processor->setConnectionParams(new Connection\Params\Tcp());
        $processor->process();
        $answer = new Http\Answer();
        return $answer->setResponse($processor->getResponse());
    }
}
