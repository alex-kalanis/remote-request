<?php

use \RemoteRequest\Protocols\Http;

class HttpProcessorMock extends \RemoteRequest\Connection\Processor
{
    public function getResponseSimple(): string
    {
        return 'HTTP/0.1 900 KO' . Http::DELIMITER . Http::DELIMITER . 'abcdefghijkl';
    }

    public function getResponseEmpty(): string
    {
        return 'HTTP/0.1 901 KO';
    }

    public function getResponseHeaders(): string
    {
        return 'HTTP/0.1 902 KO' . Http::DELIMITER
            . 'Server: PhpUnit/6.3.0' . Http::DELIMITER
            . 'Content-Length: 12' . Http::DELIMITER
            . 'Content-Type: text/plain' . Http::DELIMITER
            . 'Connection: Closed' . Http::DELIMITER
            . Http::DELIMITER
            . 'abcdefghijkl';
    }

    public function getResponseChunked(): string
    {
        return 'HTTP/0.1 903 KO' . Http::DELIMITER
            . 'Server: PhpUnit/6.3.0' . Http::DELIMITER
            . 'Content-Length: 43' . Http::DELIMITER
            . 'Content-Type: text/html' . Http::DELIMITER
            . 'Transfer-Encoding: chunked' . Http::DELIMITER
            . 'Connection: Closed' . Http::DELIMITER
            . Http::DELIMITER
            . "4\r\nWiki\r\n5\r\npedia\r\nE\r\n in\r\n\r\nchunks.\r\n0\r\n\r\n";
    }

    public function getResponseDeflated(): string
    {
        return 'HTTP/0.1 904 KO' . Http::DELIMITER
            . 'Server: PhpUnit/6.3.0' . Http::DELIMITER
            . 'Content-Length: 37' . Http::DELIMITER
            . 'Content-Type: text/plain' . Http::DELIMITER
            . 'Content-Encoding: deflate' . Http::DELIMITER
            . 'Connection: Closed' . Http::DELIMITER
            . Http::DELIMITER
            . base64_decode("S0xKTklNS8/IzMrOyc3LLygsKi4pLSuvqKwyMDQyMTUzt7AEAA==");
    }
}

class ProtocolQueryMock extends \RemoteRequest\Protocols\Http\Query
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

class HttpProcessingTest extends CommonTestClass
{
    public function testValueSimple()
    {
        $libValue1 = new \RemoteRequest\Protocols\Http\Query\Value();
        $this->assertEquals('', $libValue1);
        $libValue2 = $this->prepareTestValue('lkjhg');
        $this->assertEquals('lkjhg', $libValue2);
    }

    public function testValueFile()
    {
        $libValue1 = new \RemoteRequest\Protocols\Http\Query\File();
        $this->assertEquals('', $libValue1);
        $this->assertEquals('binary', $libValue1->getFilename());
        $this->assertEquals('octet/stream', $libValue1->getMimeType());
        $libValue2 = $this->prepareTestFile('lkjhgfdsa');
        $this->assertEquals('lkjhgfdsa', $libValue2);
        $this->assertEquals('dummy.txt', $libValue2->getFilename());
        $this->assertEquals('text/plain', $libValue2->getMimeType());
    }

    public function testQuerySimple()
    {
        $lib = $this->prepareQuerySimple();
        $lib->setHost('somewhere.example');
        $lib->setPort(80);
        $this->assertEquals("GET /example HTTP/1.1\r\nHost: somewhere.example\r\n\r\n", '' . $lib);
        $lib->setPort(444);
        $this->assertEquals("GET /example HTTP/1.1\r\nHost: somewhere.example:444\r\n\r\n", '' . $lib);
        $lib->setRequestSettings($this->prepareProtocolWrapper('disable.example', 60));
        $this->assertEquals("GET /example HTTP/1.1\r\nHost: disable.example:60\r\n\r\n", '' . $lib);
    }

    public function testQueryWithInline()
    {
        $lib = $this->prepareQuerySimple();
        $lib->setHost('somewhere.example');
        $lib->setPort(80);
        $lib->addValue('foo', 'bar');
        $this->assertEquals("GET /example?foo=bar HTTP/1.1\r\nHost: somewhere.example\r\n\r\n", '' . $lib);
        $lib->setPath('/example?baz=abc');
        $this->assertEquals("GET /example?baz=abc&foo=bar HTTP/1.1\r\nHost: somewhere.example\r\n\r\n", '' . $lib);
    }

    public function testQueryWithContent()
    {
        $lib = $this->prepareQuerySimple();
        $lib->setMultipart(false);
        $lib->setHost('somewhere.example');
        $lib->setPort(80);
        $lib->addValue('foo', 'bar');
        $this->assertEquals(
            "GET /example HTTP/1.1\r\nHost: somewhere.example\r\n"
            . "Content-Length: 7\r\n\r\nfoo=bar", '' . $lib);
        $lib->setPath('/example?baz=abc');
        $this->assertEquals(
            "GET /example?baz=abc HTTP/1.1\r\nHost: somewhere.example\r\n"
            . "Content-Length: 7\r\n\r\nfoo=bar", '' . $lib);
        $lib->addValues(['bar' => 'def', 'bay' => 'ghi']);
        $this->assertEquals(
            "GET /example?baz=abc HTTP/1.1\r\nHost: somewhere.example\r\n"
            . "Content-Length: 23\r\n\r\nfoo=bar&bar=def&bay=ghi", '' . $lib);
    }

    public function testQueryWithContentPost()
    {
        $lib = $this->prepareQuerySimple();
        $lib->setMethod('post');
        $lib->setHost('somewhere.example');
        $lib->setPort(80);
        $lib->addValue('foo', 'bar');
        $this->assertEquals(
            "POST /example HTTP/1.1\r\nHost: somewhere.example\r\n"
            . "Content-Length: 7\r\nContent-Type: application/x-www-form-urlencoded\r\n\r\n"
            . "foo=bar", '' . $lib);
        $lib->setPath('/example?baz=abc');
        $this->assertEquals(
            "POST /example?baz=abc HTTP/1.1\r\nHost: somewhere.example\r\n"
            . "Content-Length: 7\r\nContent-Type: application/x-www-form-urlencoded\r\n\r\n"
            . "foo=bar", '' . $lib);
        $lib->addValue('bar', 'def');
        $this->assertEquals(
            "POST /example?baz=abc HTTP/1.1\r\nHost: somewhere.example\r\n"
            . "Content-Length: 15\r\nContent-Type: application/x-www-form-urlencoded\r\n\r\n"
            . "foo=bar&bar=def", '' . $lib);
    }

    public function testQueryWithContentFiles()
    {
        $lib = $this->prepareQuerySimple();
        $lib->setRequestSettings($this->prepareProtocolWrapper('somewhere.example', 512))
            ->addValues(['foo' => 'bar', 'up' => $this->prepareTestFile('mnbvcx')]);
        $this->assertEquals(
            "GET /example HTTP/1.1\r\nHost: somewhere.example:512\r\nContent-Length: 202\r\n\r\n"
            . "----PHPFSock--\r\nContent-Disposition: form-data; name=\"foo\"\r\n\r\nbar\r\n"
            . "----PHPFSock--\r\nContent-Disposition: form-data; name=\"up\"; filename=\"dummy.txt\"\r\nContent-Type: text/plain\r\n\r\nmnbvcx\r\n"
            . "----PHPFSock----\r\n", '' . $lib);
    }

    public function testAnswerSimple()
    {
        $method = new HttpProcessorMock();
        $lib = $this->prepareAnswerSimple($method->getResponseSimple());
        $this->assertEquals(900, $lib->getCode());
        $this->assertEquals('abcdefghijkl', $lib->getContent());
    }

    public function testAnswerEmpty()
    {
        $method = new HttpProcessorMock();
        $lib = $this->prepareAnswerSimple($method->getResponseEmpty());
        $this->assertEquals(901, $lib->getCode());
        $this->assertEquals('', $lib->getContent());
    }

    public function testAnswerHeaders()
    {
        $method = new HttpProcessorMock();
        $lib = $this->prepareAnswerSimple($method->getResponseHeaders());
        $this->assertEquals(902, $lib->getCode());
        $this->assertEquals('abcdefghijkl', $lib->getContent());
        $this->assertEquals('text/plain', $lib->getHeader('Content-Type'));
        $this->assertEquals('Closed', $lib->getHeader('Connection'));
    }

    public function testAnswerChunked()
    {
        $method = new HttpProcessorMock();
        $lib = $this->prepareAnswerSimple($method->getResponseChunked());
        $this->assertEquals(903, $lib->getCode());
        $this->assertEquals("Wikipedia in\r\n\r\nchunks.", $lib->getContent());
        $this->assertEquals('text/html', $lib->getHeader('Content-Type'));
        $this->assertEquals('chunked', $lib->getHeader('Transfer-Encoding'));
    }

    public function testAnswerDeflated()
    {
        $method = new HttpProcessorMock();
        $lib = $this->prepareAnswerSimple($method->getResponseDeflated());
        $this->assertEquals(904, $lib->getCode());
        $this->assertEquals("abcdefghijklmnopqrstuvwxyz012456789", $lib->getContent());
        $this->assertEquals('text/plain', $lib->getHeader('Content-Type'));
        $this->assertEquals('deflate', $lib->getHeader('Content-Encoding'));
    }

    protected function prepareQuerySimple()
    {
        $lib = new ProtocolQueryMock();
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
        return (new \RemoteRequest\Protocols\Http\Answer())->setResponse($content);
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