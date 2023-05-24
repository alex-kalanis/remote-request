<?php

namespace ProtocolsTests\Restful;


use CommonTestClass;
use kalanis\RemoteRequest\Connection;
use kalanis\RemoteRequest\Protocols\Http;
use kalanis\RemoteRequest\Protocols\Restful;
use kalanis\RemoteRequest\RequestException;


class AnswerMock extends Connection\Processor
{
    public function getResponseSimple()
    {
        return CommonTestClass::stringToResource('HTTP/0.1 901 KO' . Http::DELIMITER
            . 'Server: PhpUnit/6.3.0' . Http::DELIMITER
            . 'Content-Length: 29' . Http::DELIMITER
            . 'Content-Type: text/plain' . Http::DELIMITER
            . 'Connection: Closed' . Http::DELIMITER
            . Http::DELIMITER
            . '{"foou":"barr","abcd":"efgh"}');
    }

    public function getResponseFile()
    {
        return CommonTestClass::stringToResource('HTTP/0.1 902 KO' . Http::DELIMITER
            . 'Server: PhpUnit/6.3.0' . Http::DELIMITER
            . 'Content-Length: 109' . Http::DELIMITER
            . 'Content-Type: text/plain' . Http::DELIMITER
            . 'Connection: Closed' . Http::DELIMITER
            . Http::DELIMITER
            . '{"foou":"barr","up":{"type":"file","filename":"unknown.txt","mimetype":"text\/plain","content64":"bW5idmN4"}}');
    }
}


class AnswerTest extends CommonTestClass
{
    /**
     * @throws RequestException
     */
    public function testSimple(): void
    {
        $method = new AnswerMock();
        $lib = $this->prepareAnswerSimple($method->getResponseSimple());
        $this->assertEquals(901, $lib->getCode());
        $data = $lib->getDecodedContent(true);
        $this->assertEquals('barr', $data['foou']);
        $this->assertEquals('efgh', $data['abcd']);
        $this->assertEquals('text/plain', $lib->getHeader('Content-Type'));
        $this->assertEquals('Closed', $lib->getHeader('Connection'));
    }

    /**
     * @throws RequestException
     */
    public function testFiles(): void
    {
        $method = new AnswerMock();
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

    /**
     * @param resource|string|null $content
     * @throws RequestException
     * @return Restful\Answer
     */
    protected function prepareAnswerSimple($content): Restful\Answer
    {
        return (new Restful\Answer())->setResponse($content);
    }
}
