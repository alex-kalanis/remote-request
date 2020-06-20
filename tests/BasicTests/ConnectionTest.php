<?php

use RemoteRequest\Connection;
use RemoteRequest\Pointers;
use RemoteRequest\Protocols;
use RemoteRequest\RequestException;
use RemoteRequest\Wrappers;

class ConnectProcessorMock extends Connection\Processor
{
    public function __construct(Pointers\ASocket $method = null)
    {
        parent::__construct($method);
        $this->processor = new PointerProcessorMock();
    }
}

class PointerProcessorMock extends Pointers\Processor
{
    public function processPointer($filePointer, RemoteRequest\Wrappers\AWrapper $wrapper)
    {
        $this->checkQuery();
        $this->checkPointer($filePointer);
        $this->writeRequest($filePointer, $wrapper);
        rewind($filePointer); // FOR REASON
        $this->readResponse($filePointer);
        return $this;
    }
}

class EmptyTestSocket extends Pointers\ASocket
{
    public function getRemotePointer(Wrappers\AWrapper $protocolWrapper)
    {
        return null;
    }
}

class ExceptionTestSocket extends Pointers\ASocket
{
    public function getRemotePointer(Wrappers\AWrapper $protocolWrapper)
    {
        throw new RequestException('Cannot establish connection');
    }
}

class ConnectionTest extends CommonTestClass
{
    /**
     * @throws RequestException
     */
    public function testWrappersInit()
    {
        $this->assertInstanceOf('\RemoteRequest\Wrappers\File', Wrappers\AWrapper::getWrapper(Wrappers\AWrapper::SCHEMA_FILE));
        $this->assertInstanceOf('\RemoteRequest\Wrappers\Php', Wrappers\AWrapper::getWrapper(Wrappers\AWrapper::SCHEMA_PHP));
        $this->assertInstanceOf('\RemoteRequest\Wrappers\Tcp', Wrappers\AWrapper::getWrapper(Wrappers\AWrapper::SCHEMA_TCP));
        $this->assertInstanceOf('\RemoteRequest\Wrappers\Udp', Wrappers\AWrapper::getWrapper(Wrappers\AWrapper::SCHEMA_UDP));
        $this->assertInstanceOf('\RemoteRequest\Wrappers\Ssl', Wrappers\AWrapper::getWrapper(Wrappers\AWrapper::SCHEMA_SSL));
    }

    public function testWrapperSimple()
    {
        $wrapper = new Wrappers\Tcp();
        $wrapper->setTarget('');
        $this->assertEmpty($wrapper->getHost());
        $this->assertEquals('tcp://', $wrapper->getHostname());
        $this->assertEquals(SOL_TCP, $wrapper->getProtocol());
        $wrapper->setTarget(Wrappers\Php::HOST_TEMP);
        $this->assertEquals('tcp://temp', $wrapper->getHostname());
        $this->assertEquals('temp', $wrapper->getHost());
        $this->assertEquals(1, $wrapper->getPort());
        $this->assertEquals(30, $wrapper->getTimeout());

        $lineSett = new Protocols\Http\Query();
        $wrapper->setRequest($lineSett->setHost(Wrappers\Php::HOST_MEMORY)->setPort(123456));
        $this->assertEquals('memory', $wrapper->getHost());
        $this->assertEquals(123456, $wrapper->getPort());

        $wrapper = new Wrappers\Udp();
        $this->assertEquals('udp://', $wrapper->getHostname());
        $this->assertEquals(SOL_UDP, $wrapper->getProtocol());

        $wrapper = new Wrappers\Ssl();
        $this->assertEquals('ssl://', $wrapper->getHostname());
        $this->assertEquals(SOL_TCP, $wrapper->getProtocol());

        $wrapper = new Wrappers\Php();
        $this->assertEquals('php://', $wrapper->getHostname());
        $this->assertEquals(0, $wrapper->getProtocol());
        $this->assertNull($wrapper->getPort());
        $this->assertNull($wrapper->getTimeout());

        $wrapper = new Wrappers\File();
        $this->assertEquals('file://', $wrapper->getHostname());
        $this->assertEquals(0, $wrapper->getProtocol());
        $this->assertNull($wrapper->getPort());
        $this->assertNull($wrapper->getTimeout());
    }

    /**
     * @expectedException \RemoteRequest\RequestException
     */
    public function testWrapperFail()
    {
        Wrappers\AWrapper::getWrapper('unknown');
    }

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
        $wrapper = new Wrappers\Php();
        $wrapper->setTarget(Wrappers\Php::HOST_MEMORY);
        $processor = new ConnectProcessorMock(new Pointers\Socket());
        $processor->setProtocolWrapper($wrapper);
        $processor->setData($query);
        $processor = new ConnectProcessorMock(new Pointers\SharedInternal());
        $processor->setProtocolWrapper($wrapper);
        $processor->setData($query);
        $this->assertEquals(substr($content, 0, 2000), $processor->getResponse());
    }

    public function testPointersInit()
    {
        $this->assertInstanceOf('\RemoteRequest\Pointers\SharedInternal', Pointers\ASocket::getPointer(Pointers\ASocket::SOCKET_INTERNAL));
        $this->assertInstanceOf('\RemoteRequest\Pointers\Stream', Pointers\ASocket::getPointer(Pointers\ASocket::SOCKET_STREAM));
        $this->assertInstanceOf('\RemoteRequest\Pointers\Socket', Pointers\ASocket::getPointer(Pointers\ASocket::SOCKET_SOCKET));
        $this->assertInstanceOf('\RemoteRequest\Pointers\FSocket', Pointers\ASocket::getPointer(Pointers\ASocket::SOCKET_FSOCKET));
        $this->assertInstanceOf('\RemoteRequest\Pointers\Pfsocket', Pointers\ASocket::getPointer(Pointers\ASocket::SOCKET_PFSOCKET));
    }

    public function testPointersSet()
    {
        $pointer = new Pointers\Fsocket();
        $this->assertInstanceOf('\RemoteRequest\Pointers\FSocket', $pointer);

        $pointer = new Pointers\Pfsocket();
        $this->assertInstanceOf('\RemoteRequest\Pointers\Pfsocket', $pointer);

        $pointer = new Pointers\Stream();
        $pointer->setContextOptions(['foo' => 'bar', 'baz' => 'eff']);
        $this->assertInstanceOf('\RemoteRequest\Pointers\Stream', $pointer);
    }

    /**
     * When it blows
     * @expectedException \RemoteRequest\RequestException
     */
    public function testCallException()
    {
        $processor = new Connection\Processor(new ExceptionTestSocket());
        $processor->setProtocolWrapper(new Wrappers\File());
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
        $processor->setProtocolWrapper(new Wrappers\File());
        $processor->setData(new Protocols\Dummy\Query());
        $processor->getResponse(); // die
    }

    /**
     * @param string|null $message what to send to remote machine
     * @return string
     * @throws RequestException
     */
    protected function queryOnMock(?string $message)
    {
        $wrapper = new Wrappers\Php();
        $wrapper->setTarget(Wrappers\Php::HOST_MEMORY);
        $processor = new ConnectProcessorMock(new Pointers\SharedInternal());
        $processor->setProtocolWrapper($wrapper);
        if (!is_null($message)) {
            $query = new Protocols\Dummy\Query();
            $query->body = $message;
            $processor->setData($query);
        }
        return $processor->getResponse();
    }
}