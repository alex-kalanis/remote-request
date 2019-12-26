<?php

use \RemoteRequest\Protocols\Http;

class TestProcessor extends \RemoteRequest\Connection\Processor
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

class HttpQueryTest extends CommonTestClass
{
    /**
     * When the answer is empty
     */
    public function testSetsSimple()
    {
        $result = $this->queryOnMock(new TestProcessor());
        $this->assertEquals(900, $result->getCode());
        $this->assertEquals('', $result->getContent());
    }

    /**
     * When the answer contains something
     */
    public function testSetsBody()
    {
        $result = $this->queryOnMock(new ContentTestProcessor());
        $this->assertEquals(901, $result->getCode());
        $this->assertEquals('abcdefghijkl', $result->getContent());
    }

    /**
     * @param \RemoteRequest\Connection\Processor $processor cim vrati vzdalena data
     * @return \RemoteRequest\Protocols\Http\Answer
     * @throws \RemoteRequest\RequestException
     */
    protected function queryOnMock(\RemoteRequest\Connection\Processor $processor)
    {
        $processor->setData(new \RemoteRequest\Protocols\Dummy\Query());
        $processor->setProtocolWrapper(new \RemoteRequest\Wrappers\Tcp());
        $answer = new \RemoteRequest\Protocols\Http\Answer();
        return $answer->setResponse($processor->getResponse());
    }

}