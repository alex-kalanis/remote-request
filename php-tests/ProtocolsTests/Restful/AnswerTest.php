<?php

namespace tests\ProtocolsTests\Restful;


use tests\CommonTestClass;
use kalanis\RemoteRequest\Protocols\Restful;
use kalanis\RemoteRequest\RequestException;


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
