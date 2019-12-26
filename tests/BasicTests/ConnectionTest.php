<?php

class ConnectProcessorMock extends \RemoteRequest\Connection\Processor
{
    public function __construct(\RemoteRequest\Pointers\APointer $method = null)
    {
        parent::__construct($method);
        $this->processor = new PointerProcessorMock();
    }
}

class PointerProcessorMock extends \RemoteRequest\Pointers\Processor
{
    public function processPointer($filePointer)
    {
        $this->checkQuery();
        $this->checkPointer($filePointer);
        $this->writeRequest($filePointer);
        rewind($filePointer); // FOR REASON
        $this->readResponse($filePointer);
        return $this;
    }
}

class EmptyTestPointer extends \RemoteRequest\Pointers\APointer
{
    public function getRemotePointer(\RemoteRequest\Wrappers\AWrapper $protocolWrapper)
    {
        return null;
    }
}

class ExceptionTestPointer extends \RemoteRequest\Pointers\APointer
{
    public function getRemotePointer(\RemoteRequest\Wrappers\AWrapper $protocolWrapper)
    {
        throw new \RemoteRequest\RequestException('Cannot establish connection');
    }
}

class ConnectionTest extends CommonTestClass
{
    /**
     * When it runs
     * The response is in query init
     * @throws \RemoteRequest\RequestException
     */
    public function testSetsSimple()
    {
        $this->assertEquals('', $this->queryOnMock(''));
        $this->assertEquals('abcdefghijkl', $this->queryOnMock('abcdefghijkl'));
        $this->assertEquals('Hello.', $this->queryOnMock('Hello.'));
    }

    /**
     * When it blows
     * @expectedException \RemoteRequest\RequestException
     */
    public function testCallException()
    {
        $processor = new \RemoteRequest\Connection\Processor(new ExceptionTestPointer());
        $processor->setProtocolWrapper(new \RemoteRequest\Wrappers\File());
        $processor->setData(new RemoteRequest\Protocols\Dummy\Query());
        $processor->getResponse(); // die
    }

    /**
     * When it blows
     * @expectedException \RemoteRequest\RequestException
     */
    public function testCallNoPointer()
    {
        $processor = new \RemoteRequest\Connection\Processor(new EmptyTestPointer());
        $processor->setProtocolWrapper(new \RemoteRequest\Wrappers\File());
        $processor->setData(new RemoteRequest\Protocols\Dummy\Query());
        $processor->getResponse(); // die
    }

    /**
     * @param string $message what to send to remote machine
     * @return string
     * @throws \RemoteRequest\RequestException
     */
    protected function queryOnMock(string $message)
    {
        $query = new \RemoteRequest\Protocols\Dummy\Query();
        $query->body = $message;
        $wrapper = new \RemoteRequest\Wrappers\Php();
        $wrapper->setTarget(\RemoteRequest\Wrappers\Php::HOST_MEMORY);
        $processor = new ConnectProcessorMock(new \RemoteRequest\Pointers\SharedInternal());
        $processor->setProtocolWrapper($wrapper);
        $processor->setData($query);
        return $processor->getResponse();
    }
}