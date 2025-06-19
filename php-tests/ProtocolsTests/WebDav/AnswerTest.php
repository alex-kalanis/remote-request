<?php

namespace tests\ProtocolsTests\WebDav;


use tests\CommonTestClass;
use kalanis\RemoteRequest\Protocols\WebDAV;
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
