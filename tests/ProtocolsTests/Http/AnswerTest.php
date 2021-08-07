<?php

namespace ProtocolsTests\Http;


use CommonTestClass;
use RemoteRequest\Connection;
use RemoteRequest\Protocols\Http;


class AnswerMock extends Connection\Processor
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


class AnswerTest extends CommonTestClass
{
    public function testSimple(): void
    {
        $method = new AnswerMock();
        $lib = $this->prepareSimple($method->getResponseSimple());
        $this->assertEquals(900, $lib->getCode());
        $this->assertEquals('abcdefghijkl', $lib->getContent());
    }

    public function testEmpty(): void
    {
        $method = new AnswerMock();
        $lib = $this->prepareSimple($method->getResponseEmpty());
        $this->assertEquals(901, $lib->getCode());
        $this->assertEquals('', $lib->getContent());
    }

    public function testHeaders(): void
    {
        $method = new AnswerMock();
        $lib = $this->prepareSimple($method->getResponseHeaders());
        $this->assertEquals(902, $lib->getCode());
        $this->assertEquals('abcdefghijkl', $lib->getContent());
        $this->assertEquals('text/plain', $lib->getHeader('Content-Type'));
        $this->assertEquals('Closed', $lib->getHeader('Connection'));
        $this->assertEquals('unknown', $lib->getHeader('what', 'unknown'));

        $this->assertNotEmpty($lib->getHeaders('Connection'));
        $this->assertEmpty($lib->getHeaders('unknown'));
        $this->assertArrayHasKey('Connection', $lib->getAllHeaders());
        $this->assertArrayNotHasKey('unknown', $lib->getAllHeaders());
        $this->assertFalse($lib->isSuccessful());
    }

    public function testChunked(): void
    {
        $method = new AnswerMock();
        $lib = $this->prepareSimple($method->getResponseChunked());
        $this->assertEquals(903, $lib->getCode());
        $this->assertEquals("Wikipedia in\r\n\r\nchunks.", $lib->getContent());
        $this->assertEquals('text/html', $lib->getHeader('Content-Type'));
        $this->assertEquals('chunked', $lib->getHeader('Transfer-Encoding'));
    }

    public function testDeflated(): void
    {
        $method = new AnswerMock();
        $lib = $this->prepareSimple($method->getResponseDeflated());
        $this->assertEquals(904, $lib->getCode());
        $this->assertEquals("abcdefghijklmnopqrstuvwxyz012456789", $lib->getContent());
        $this->assertEquals('text/plain', $lib->getHeader('Content-Type'));
        $this->assertEquals('deflate', $lib->getHeader('Content-Encoding'));
    }

    protected function prepareSimple(string $content): Http\Answer
    {
        return (new Http\Answer())->setResponse($content);
    }
}
