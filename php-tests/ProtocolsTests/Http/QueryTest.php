<?php

namespace ProtocolsTests\Http;


use CommonTestClass;
use kalanis\RemoteRequest\Connection;
use kalanis\RemoteRequest\Protocols\Http;


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


class QueryAuthBasicMock extends Http\Query\AuthBasic
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


class QueryAuthDigestMock extends Http\Query\AuthDigest
{
    /**
     * Overwrite because random string in testing does not work
     * @return string
     */
    protected function generateBoundary(): string
    {
        return '--PHPFSock--';
    }

    protected function getRandomString(): string
    {
        return '0a4f113b';
    }
}


class QueryTest extends CommonTestClass
{
    public function testQuerySimple(): void
    {
        $lib = $this->prepareSimple();
        $lib->setHost('somewhere.example');
        $lib->setPort(80);
        $this->assertEquals("GET /example HTTP/1.1\r\nHost: somewhere.example\r\n\r\n"
            , stream_get_contents($lib->getData(), -1, 0));
        $lib->setPort(444);
        $this->assertEquals("GET /example HTTP/1.1\r\nHost: somewhere.example:444\r\n\r\n"
            , stream_get_contents($lib->getData(), -1, 0));
        $lib->setRequestSettings($this->prepareProtocolSchema('disable.example', 60));
        $this->assertEquals("GET /example HTTP/1.1\r\nHost: disable.example:60\r\n\r\n"
            , stream_get_contents($lib->getData(), -1, 0));
        $this->assertEquals("PUT", $lib->setMethod('put')->getMethod());
        $this->assertEquals("PUT", $lib->setMethod('unknown')->getMethod());

        $lib->addHeader('some', 'value');
        $this->assertNotEquals(false, strpos(stream_get_contents($lib->getData(), -1, 0), 'some: value'));
        $lib->removeHeader('some');
        $this->assertFalse(strpos(stream_get_contents($lib->getData(), -1, 0), 'some: value')); // not found
        $sett = new Connection\Params\Ssl();
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
        $lib->setInline(true);
        $this->assertEquals("GET /example?foo=bar HTTP/1.1\r\nHost: somewhere.example\r\n\r\n"
            , stream_get_contents($lib->getData(), -1, 0));
        $lib->setPath('/example?baz=abc');
        $this->assertEquals("GET /example?baz=abc&foo=bar HTTP/1.1\r\nHost: somewhere.example\r\n\r\n"
            , stream_get_contents($lib->getData(), -1, 0));
    }

    public function testQueryWithContent(): void
    {
        $lib = $this->prepareSimple();
        $lib->setInline(false);
        $lib->setHost('somewhere.example');
        $lib->setPort(80);
        $lib->addValue('foo', 'bar');
        $this->assertEquals(
            "GET /example HTTP/1.1\r\nHost: somewhere.example\r\n"
            . "Content-Length: 7\r\n\r\nfoo=bar"
            , stream_get_contents($lib->getData(), -1, 0));
        $lib->setPath('/example?baz=abc');
        $this->assertEquals(
            "GET /example?baz=abc HTTP/1.1\r\nHost: somewhere.example\r\n"
            . "Content-Length: 7\r\n\r\nfoo=bar"
            , stream_get_contents($lib->getData(), -1, 0));
        $lib->addValues(['bar' => 'def', 'bay' => 'ghi']);
        $this->assertEquals(
            "GET /example?baz=abc HTTP/1.1\r\nHost: somewhere.example\r\n"
            . "Content-Length: 23\r\n\r\nfoo=bar&bar=def&bay=ghi"
            , stream_get_contents($lib->getData(), -1, 0));
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
            . "foo=bar"
            , stream_get_contents($lib->getData(), -1, 0));
        $lib->setPath('/example?baz=abc');
        $this->assertEquals(
            "POST /example?baz=abc HTTP/1.1\r\nHost: somewhere.example\r\n"
            . "Content-Length: 7\r\nContent-Type: application/x-www-form-urlencoded\r\n\r\n"
            . "foo=bar"
            , stream_get_contents($lib->getData(), -1, 0));
        $lib->addValue('bar', 'def');
        $this->assertEquals(
            "POST /example?baz=abc HTTP/1.1\r\nHost: somewhere.example\r\n"
            . "Content-Length: 15\r\nContent-Type: application/x-www-form-urlencoded\r\n\r\n"
            . "foo=bar&bar=def"
            , stream_get_contents($lib->getData(), -1, 0));
        $lib->removeValue('bar');
        $this->assertEquals(
            "POST /example?baz=abc HTTP/1.1\r\nHost: somewhere.example\r\n"
            . "Content-Length: 7\r\nContent-Type: application/x-www-form-urlencoded\r\n\r\n"
            . "foo=bar"
            , stream_get_contents($lib->getData(), -1, 0));
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
            . "----PHPFSock----\r\n"
            , stream_get_contents($lib->getData(), -1, 0));
    }

    public function testQueryWithAuth(): void
    {
        $lib = $this->prepareAuthBasic();
        $lib->setRequestSettings($this->prepareProtocolSchema('somewhere.example', 126))
            ->addValues(['foo' => 'bar']);
        $lib->setInline(true);
        $this->assertEquals(
            "GET /example?foo=bar HTTP/1.1\r\nHost: somewhere.example:126\r\n"
            . "Authorization: Basic Zm9vZGlkOmJ1dWdnZWU=\r\n\r\n"
            , stream_get_contents($lib->getData(), -1, 0));
    }

    public function testQueryWithDigest(): void
    {
        $lib = $this->prepareAuthDigest();
        $lib->setRequestSettings($this->prepareProtocolSchema('localhost'));
        $this->assertEquals(
            "GET /dir/index.html HTTP/1.1\r\nHost: localhost\r\nAuthorization: Digest username=\"Mufasa\", realm=\"testrealm@host.com\", nonce=\"dcd98b7102dd2f0e8b11d0f600bfb0c093\", uri=\"/dir/index.html\", qop=\"auth\", nc=\"00000001\", cnonce=\"0a4f113b\", response=\"6629fae49393a05397450978507c4ef1\", opaque=\"5ccc069c403ebaf9f0171e9517f40e41\"\r\n\r\n"
            , stream_get_contents($lib->getData(), -1, 0));
    }

    protected function prepareSimple(): Http\Query
    {
        $lib = new QueryMock();
        $lib->setMethod('get');
        $lib->setPath('/example');
        $lib->setInline(false);
        $lib->removeHeader('Accept');
        $lib->removeHeader('User-Agent');
        $lib->removeHeader('Connection');
        return $lib;
    }

    protected function prepareAuthBasic(): Http\Query
    {
        $lib = new QueryAuthBasicMock();
        $lib->setMethod('get');
        $lib->setPath('/example');
        $lib->setInline(false);
        $lib->setCredentials('foodid', 'buuggee');
        $lib->removeHeader('Accept');
        $lib->removeHeader('User-Agent');
        $lib->removeHeader('Connection');
        return $lib;
    }

    protected function prepareAuthDigest(): Http\Query
    {
        $lib = new QueryAuthDigestMock();
        $lib->setMethod('get');
        $lib->setPath('/dir/index.html');
        $lib->setInline(false);
        $lib->setCredentials('Mufasa', 'Circle Of Life', 'testrealm@host.com');
        $lib->setProperties('dcd98b7102dd2f0e8b11d0f600bfb0c093', '5ccc069c403ebaf9f0171e9517f40e41', 'auth');
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
        return $request->setTarget($host, $port);
    }
}
