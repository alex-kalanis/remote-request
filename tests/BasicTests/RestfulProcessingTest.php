<?php

use \RemoteRequest\Protocols\Http;

class RestfulProcessorMock extends \RemoteRequest\Connection\Processor
{
    public function getResponseSimple(): string
    {
        return 'HTTP/0.1 901 KO' . Http::DELIMITER
            . 'Server: PhpUnit/6.3.0' . Http::DELIMITER
            . 'Content-Length: 29' . Http::DELIMITER
            . 'Content-Type: text/plain' . Http::DELIMITER
            . 'Connection: Closed' . Http::DELIMITER
            . Http::DELIMITER
            . '{"foou":"barr","abcd":"efgh"}';
    }

    public function getResponseFile(): string
    {
        return 'HTTP/0.1 902 KO' . Http::DELIMITER
            . 'Server: PhpUnit/6.3.0' . Http::DELIMITER
            . 'Content-Length: 109' . Http::DELIMITER
            . 'Content-Type: text/plain' . Http::DELIMITER
            . 'Connection: Closed' . Http::DELIMITER
            . Http::DELIMITER
            . '{"foou":"barr","up":{"type":"file","filename":"unknown.txt","mimetype":"text\/plain","content64":"bW5idmN4"}}';
    }
}

class RestProtocolQueryMock extends \RemoteRequest\Protocols\Restful\Query
{
}

class RestfulProcessingTest extends CommonTestClass
{
    public function testQuerySimple()
    {
        $lib = $this->prepareQuerySimple();
        $lib->setHost('somewhere.example');
        $lib->setPort(80);
        $lib->addValues(['foo' => 'bar', 'abc' => 'def',]);

        $this->assertEquals("GET /example HTTP/1.1\r\nHost: somewhere.example\r\nContent-Length: 25\r\n\r\n"
            . '{"foo":"bar","abc":"def"}', $lib->getData());
        $lib->setPort(444);
        $this->assertEquals("GET /example HTTP/1.1\r\nHost: somewhere.example:444\r\nContent-Length: 25\r\n\r\n"
            . '{"foo":"bar","abc":"def"}', $lib->getData());
        $lib->setPath('/example?baz=abc');
        $this->assertEquals("GET /example?baz=abc HTTP/1.1\r\nHost: somewhere.example:444\r\nContent-Length: 25\r\n\r\n"
            . '{"foo":"bar","abc":"def"}', $lib->getData());
    }

    public function testQueryWithContentFiles()
    {
        $lib = $this->prepareQuerySimple();
        $lib->setRequestSettings($this->prepareProtocolWrapper('somewhere.example', 512))
            ->addValues(['foo' => 'bar', 'up' => $this->prepareTestFile('mnbvcx')]);
        $this->assertEquals(
            "GET /example HTTP/1.1\r\nHost: somewhere.example:512\r\nContent-Length: 105\r\n\r\n"
            . '{"foo":"bar","up":{"type":"file","filename":"dummy.txt","mimetype":"text\/plain","content64":"bW5idmN4"}}', $lib->getData());
        $lib->setPath('/example?baz=abc');
        $this->assertEquals(
            "GET /example?baz=abc HTTP/1.1\r\nHost: somewhere.example:512\r\nContent-Length: 105\r\n\r\n"
            . '{"foo":"bar","up":{"type":"file","filename":"dummy.txt","mimetype":"text\/plain","content64":"bW5idmN4"}}', $lib->getData());
    }

    public function testAnswerSimple()
    {
        $method = new RestfulProcessorMock();
        $lib = $this->prepareAnswerSimple($method->getResponseSimple());
        $this->assertEquals(901, $lib->getCode());
        $data = $lib->getDecodedContent(true);
        $this->assertEquals('barr', $data['foou']);
        $this->assertEquals('efgh', $data['abcd']);
        $this->assertEquals('text/plain', $lib->getHeader('Content-Type'));
        $this->assertEquals('Closed', $lib->getHeader('Connection'));
    }

    public function testAnswerFiles()
    {
        $method = new RestfulProcessorMock();
        $lib = $this->prepareAnswerSimple($method->getResponseFile());
        $this->assertEquals(902, $lib->getCode());
        $data = $lib->getDecodedContent();
        $this->assertEquals('barr', $data->foou);
        $this->assertInstanceOf('\stdClass', $data->up);
        $this->assertEquals('file', $data->up->type);
        $this->assertEquals('unknown.txt', $data->up->filename);
        $this->assertEquals('text/plain', $data->up->mimetype);
        $this->assertEquals('mnbvcx', base64_decode($data->up->content64));
        $this->assertEquals('text/plain', $lib->getHeader('Content-Type'));
        $this->assertEquals('Closed', $lib->getHeader('Connection'));
    }

    protected function prepareQuerySimple()
    {
        $lib = new RestProtocolQueryMock();
        $lib->setMethod('get');
        $lib->setPath('/example');
        $lib->setMultipart(null);
        $lib->removeHeader('Accept');
        $lib->removeHeader('User-Agent');
        $lib->removeHeader('Connection');
        return $lib;
    }

    protected function prepareAnswerSimple(string $content)
    {
        return (new \RemoteRequest\Protocols\Restful\Answer())->setResponse($content);
    }

    protected function prepareTestValue($content)
    {
        return new \RemoteRequest\Protocols\Http\Query\Value($content);
    }

    protected function prepareTestFile($content)
    {
        $libValue = new \RemoteRequest\Protocols\Http\Query\File($content);
        $libValue->filename = 'dummy.txt';
        $libValue->mime = 'text/plain';
        return $libValue;
    }

    protected function prepareProtocolWrapper(string $host = 'unable.example', int $port = 80)
    {
        $request = new \RemoteRequest\Wrappers\Tcp();
        return $request->setTarget($host, $port);
    }
}