<?php

namespace ProtocolsTests\Restful;

use CommonTestClass;
use RemoteRequest\Protocols\Http;
use RemoteRequest\Protocols\Restful;
use RemoteRequest\Schemas;

class QueryMock extends Restful\Query
{
}

class QueryTest extends CommonTestClass
{
    public function testSimple()
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

    public function testFiles()
    {
        $lib = $this->prepareQuerySimple();
        $lib->setRequestSettings($this->prepareProtocolSchema('somewhere.example', 512))
            ->addValues(['foo' => 'bar', 'up' => $this->prepareTestFile('mnbvcx')]);
        $this->assertEquals(
            "GET /example HTTP/1.1\r\nHost: somewhere.example:512\r\nContent-Length: 105\r\n\r\n"
            . '{"foo":"bar","up":{"type":"file","filename":"dummy.txt","mimetype":"text\/plain","content64":"bW5idmN4"}}', $lib->getData());
        $lib->setPath('/example?baz=abc');
        $this->assertEquals(
            "GET /example?baz=abc HTTP/1.1\r\nHost: somewhere.example:512\r\nContent-Length: 105\r\n\r\n"
            . '{"foo":"bar","up":{"type":"file","filename":"dummy.txt","mimetype":"text\/plain","content64":"bW5idmN4"}}', $lib->getData());
    }

    protected function prepareQuerySimple()
    {
        $lib = new QueryMock();
        $lib->setMethod('get');
        $lib->setPath('/example');
        $lib->setMultipart(null);
        $lib->removeHeader('Accept');
        $lib->removeHeader('User-Agent');
        $lib->removeHeader('Connection');
        return $lib;
    }

    protected function prepareTestFile($content)
    {
        $libValue = new Http\Query\File($content);
        $libValue->filename = 'dummy.txt';
        $libValue->mime = 'text/plain';
        return $libValue;
    }

    protected function prepareProtocolSchema(string $host = 'unable.example', int $port = 80)
    {
        $request = new Schemas\Tcp();
        return $request->setTarget($host, $port);
    }
}