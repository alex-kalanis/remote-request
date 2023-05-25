<?php

namespace ProtocolsTests\WebDav;


use CommonTestClass;
use kalanis\RemoteRequest\Connection;
use kalanis\RemoteRequest\Protocols\Http;
use kalanis\RemoteRequest\Protocols\WebDAV;
use kalanis\RemoteRequest\RequestException;


class QueryMock extends WebDAV\Query
{
    /**
     * Overwrite because random string in testing does not work
     * @return string
     */
    protected function generateBoundary(): string
    {
        return '--PHPFSock--';
    }
}


class QueryTest extends CommonTestClass
{
    /**
     * @throws RequestException
     */
    public function testSimple(): void
    {
        $lib = $this->prepareQuerySimple();
        $lib->setHost('somewhere.example');
        $lib->setPort(80);
        $lib->addValues(['foo' => 'bar', 'abc' => 'def',]);

        $this->assertEquals("GET /example HTTP/1.1\r\nHost: somewhere.example\r\nContent-Length: 15\r\n\r\n"
            . 'foo=bar&abc=def'
            , stream_get_contents($lib->getData(), -1, 0));
        $lib->setPort(444);
        $this->assertEquals("GET /example HTTP/1.1\r\nHost: somewhere.example:444\r\nContent-Length: 15\r\n\r\n"
            . 'foo=bar&abc=def'
            , stream_get_contents($lib->getData(), -1, 0));
        $lib->setPath('/example?baz=abc');
        $this->assertEquals("GET /example?baz=abc HTTP/1.1\r\nHost: somewhere.example:444\r\nContent-Length: 15\r\n\r\n"
            . 'foo=bar&abc=def'
            , stream_get_contents($lib->getData(), -1, 0));
    }

    /**
     * @throws RequestException
     */
    public function testFiles(): void
    {
        $lib = $this->prepareQuerySimple();
        $lib->setRequestSettings($this->prepareProtocolSchema('somewhere.example', 512))
            ->addValues(['foo' => 'bar', 'up' => $this->prepareTestFile('mnbvcx')]);
        $this->assertEquals(
            "GET /example HTTP/1.1\r\nHost: somewhere.example:512\r\nContent-Length: 202\r\n\r\n"
            . "----PHPFSock--\r\nContent-Disposition: form-data; name=\"foo\"\r\n\r\nbar\r\n"
            . "----PHPFSock--\r\nContent-Disposition: form-data; name=\"up\"; filename=\"dummy.txt\"\r\nContent-Type: text/plain\r\n\r\nmnbvcx\r\n"
            . "----PHPFSock----\r\n"
            , stream_get_contents($lib->getData(), -1, 0));
        $lib->setPath('/example?baz=abc');
        $this->assertEquals(
            "GET /example?baz=abc HTTP/1.1\r\nHost: somewhere.example:512\r\nContent-Length: 202\r\n\r\n"
            . "----PHPFSock--\r\nContent-Disposition: form-data; name=\"foo\"\r\n\r\nbar\r\n"
            . "----PHPFSock--\r\nContent-Disposition: form-data; name=\"up\"; filename=\"dummy.txt\"\r\nContent-Type: text/plain\r\n\r\nmnbvcx\r\n"
            . "----PHPFSock----\r\n"
            , stream_get_contents($lib->getData(), -1, 0));
    }

    protected function prepareQuerySimple(): WebDAV\Query
    {
        $lib = new QueryMock();
        $lib->setMethod('get');
        $lib->setPath('/example');
        $lib->setInline(true);
        $lib->removeHeader('Accept');
        $lib->removeHeader('User-Agent');
        $lib->removeHeader('Connection');
        return $lib;
    }

    protected function prepareTestFile($content): Http\Query\File
    {
        $libValue = new Http\Query\File($content);
        $libValue->filename = 'dummy.txt';
        $libValue->mime = 'text/plain';
        return $libValue;
    }

    protected function prepareProtocolSchema(string $host = 'unable.example', int $port = 80): Connection\Params\Tcp
    {
        $request = new Connection\Params\Tcp();
        $request->setTarget($host, $port);
        return $request;
    }
}
