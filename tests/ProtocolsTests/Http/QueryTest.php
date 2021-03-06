<?php

namespace ProtocolsTests\Http;


use CommonTestClass;
use RemoteRequest\Protocols\Http;
use RemoteRequest\Schemas;


class QueryMock extends Http\Query
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


class QueryAuthMock extends Http\Query\AuthBasic
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
    public function testQuerySimple(): void
    {
        $lib = $this->prepareSimple();
        $lib->setHost('somewhere.example');
        $lib->setPort(80);
        $this->assertEquals("GET /example HTTP/1.1\r\nHost: somewhere.example\r\n\r\n", $lib->getData());
        $lib->setPort(444);
        $this->assertEquals("GET /example HTTP/1.1\r\nHost: somewhere.example:444\r\n\r\n", $lib->getData());
        $lib->setRequestSettings($this->prepareProtocolSchema('disable.example', 60));
        $this->assertEquals("GET /example HTTP/1.1\r\nHost: disable.example:60\r\n\r\n", $lib->getData());
        $this->assertEquals("PUT", $lib->setMethod('put')->getMethod());
        $this->assertEquals("PUT", $lib->setMethod('unknown')->getMethod());

        $this->assertTrue($lib->setMultipart(null)->isInline());
        $this->assertFalse($lib->setMultipart(null)->isMultipart());
        $this->assertFalse($lib->setMultipart(true)->isInline());
        $this->assertTrue($lib->setMultipart(true)->isMultipart());
        $this->assertFalse($lib->setMultipart(false)->isInline());
        $this->assertFalse($lib->setMultipart(false)->isMultipart());

        $lib->addHeader('some', 'value');
        $this->assertNotEquals(false, strpos($lib->getData(), 'some: value'));
        $lib->removeHeader('some');
        $this->assertFalse(strpos($lib->getData(), 'some: value')); // not found
        $sett = new Schemas\Ssl();
        $sett->setTarget('elsewhere.example', 2121);
        $lib->setRequestSettings($sett);
        $this->assertEquals('elsewhere.example', $lib->getHost());
        $this->assertEquals(2121, $lib->getPort());
    }

    public function testQueryWithInline(): void
    {
        $lib = $this->prepareSimple();
        $lib->setHost('somewhere.example');
        $lib->setPort(80);
        $lib->addValue('foo', 'bar');
        $this->assertEquals("GET /example?foo=bar HTTP/1.1\r\nHost: somewhere.example\r\n\r\n", $lib->getData());
        $lib->setPath('/example?baz=abc');
        $this->assertEquals("GET /example?baz=abc&foo=bar HTTP/1.1\r\nHost: somewhere.example\r\n\r\n", $lib->getData());
    }

    public function testQueryWithContent(): void
    {
        $lib = $this->prepareSimple();
        $lib->setMultipart(false);
        $lib->setHost('somewhere.example');
        $lib->setPort(80);
        $lib->addValue('foo', 'bar');
        $this->assertEquals(
            "GET /example HTTP/1.1\r\nHost: somewhere.example\r\n"
            . "Content-Length: 7\r\n\r\nfoo=bar", $lib->getData());
        $lib->setPath('/example?baz=abc');
        $this->assertEquals(
            "GET /example?baz=abc HTTP/1.1\r\nHost: somewhere.example\r\n"
            . "Content-Length: 7\r\n\r\nfoo=bar", $lib->getData());
        $lib->addValues(['bar' => 'def', 'bay' => 'ghi']);
        $this->assertEquals(
            "GET /example?baz=abc HTTP/1.1\r\nHost: somewhere.example\r\n"
            . "Content-Length: 23\r\n\r\nfoo=bar&bar=def&bay=ghi", $lib->getData());
    }

    public function testQueryWithContentPost(): void
    {
        $lib = $this->prepareSimple();
        $lib->setMethod('post');
        $lib->setHost('somewhere.example');
        $lib->setPort(80);
        $lib->addValue('foo', 'bar');
        $this->assertEquals(
            "POST /example HTTP/1.1\r\nHost: somewhere.example\r\n"
            . "Content-Length: 7\r\nContent-Type: application/x-www-form-urlencoded\r\n\r\n"
            . "foo=bar", $lib->getData());
        $lib->setPath('/example?baz=abc');
        $this->assertEquals(
            "POST /example?baz=abc HTTP/1.1\r\nHost: somewhere.example\r\n"
            . "Content-Length: 7\r\nContent-Type: application/x-www-form-urlencoded\r\n\r\n"
            . "foo=bar", $lib->getData());
        $lib->addValue('bar', 'def');
        $this->assertEquals(
            "POST /example?baz=abc HTTP/1.1\r\nHost: somewhere.example\r\n"
            . "Content-Length: 15\r\nContent-Type: application/x-www-form-urlencoded\r\n\r\n"
            . "foo=bar&bar=def", $lib->getData());
        $lib->removeValue('bar');
        $this->assertEquals(
            "POST /example?baz=abc HTTP/1.1\r\nHost: somewhere.example\r\n"
            . "Content-Length: 7\r\nContent-Type: application/x-www-form-urlencoded\r\n\r\n"
            . "foo=bar", $lib->getData());
    }

    public function testQueryWithContentFiles(): void
    {
        $lib = $this->prepareSimple();
        $lib->setRequestSettings($this->prepareProtocolSchema('somewhere.example', 512))
            ->addValues(['foo' => 'bar', 'up' => $this->prepareTestFile('mnbvcx')]);
        $this->assertEquals(
            "GET /example HTTP/1.1\r\nHost: somewhere.example:512\r\nContent-Length: 202\r\n\r\n"
            . "----PHPFSock--\r\nContent-Disposition: form-data; name=\"foo\"\r\n\r\nbar\r\n"
            . "----PHPFSock--\r\nContent-Disposition: form-data; name=\"up\"; filename=\"dummy.txt\"\r\nContent-Type: text/plain\r\n\r\nmnbvcx\r\n"
            . "----PHPFSock----\r\n", $lib->getData());
    }

    public function testQueryWithAuth(): void
    {
        $lib = $this->prepareAuth();
        $lib->setRequestSettings($this->prepareProtocolSchema('somewhere.example', 126))
            ->addValues(['foo' => 'bar']);
        $this->assertEquals(
            "GET /example?foo=bar HTTP/1.1\r\nHost: somewhere.example:126\r\n"
            . "Authorization: Basic Zm9vZGlkOmJ1dWdnZWU=\r\n\r\n"
            , $lib->getData());
    }

    protected function prepareSimple(): Http\Query
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

    protected function prepareAuth(): Http\Query
    {
        $lib = new QueryAuthMock();
        $lib->setMethod('get');
        $lib->setPath('/example');
        $lib->setMultipart(null);
        $lib->setCredentials('foodid', 'buuggee');
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

    protected function prepareProtocolSchema(string $host = 'unable.example', int $port = 80): Schemas\Tcp
    {
        $request = new Schemas\Tcp();
        return $request->setTarget($host, $port);
    }
}