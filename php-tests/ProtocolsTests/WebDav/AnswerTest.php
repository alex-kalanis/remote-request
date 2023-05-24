<?php

namespace ProtocolsTests\WebDav;


use CommonTestClass;
use kalanis\RemoteRequest\Connection;
use kalanis\RemoteRequest\Protocols\Http;
use kalanis\RemoteRequest\Protocols\WebDAV;
use kalanis\RemoteRequest\RequestException;
use kalanis\RemoteRequest\Translations;


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
    }

    /**
     * @throws RequestException
     */
    public function testFiles(): void
    {
        $method = new AnswerMock();
        $lib = $this->prepareAnswerSimple($method->getResponseFile());
        $this->assertEquals(902, $lib->getCode());
    }

    /**
     * @param resource|string|null $content
     * @throws RequestException
     * @return WebDAV\Answer
     */
    protected function prepareAnswerSimple($content): WebDAV\Answer
    {
        return (new WebDAV\Answer())->setResponse($content);
    }
}
