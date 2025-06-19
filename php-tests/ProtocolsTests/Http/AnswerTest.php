<?php

namespace tests\ProtocolsTests\Http;


use tests\CommonTestClass;
use kalanis\RemoteRequest\Protocols\Http;
use kalanis\RemoteRequest\Translations;
use kalanis\RemoteRequest\RequestException;


class AnswerTest extends CommonTestClass
{
    /**
     * @throws RequestException
     */
    public function testSimple(): void
    {
        $method = new Answer\AnswerMock();
        $lib = $this->prepareSimple($method->getResponseSimple());
        $this->assertEquals(900, $lib->getCode());
        $this->assertEquals('KO', $lib->getReason());
        $this->assertEquals('abcdefghijkl', $lib->getContent());
    }

    /**
     * @throws RequestException
     */
    public function testSimpleStream(): void
    {
        $method = new Answer\AnswerMock();
        $lib = $this->prepareSimple(CommonTestClass::stringToResource($method->getResponseSimple()));
        $this->assertEquals(900, $lib->getCode());
        $this->assertEquals('KO', $lib->getReason());
        $this->assertEquals('abcdefghijkl', $lib->getContent());
    }

    /**
     * @throws RequestException
     */
    public function testEmpty(): void
    {
        $method = new Answer\AnswerMock();
        $lib = $this->prepareSimple($method->getResponseEmpty());
        $this->assertEquals(901, $lib->getCode());
        $this->assertEquals('', $lib->getContent());
    }

    /**
     * @throws RequestException
     */
    public function testEmptyStream(): void
    {
        $method = new Answer\AnswerMock();
        $lib = $this->prepareSimple(CommonTestClass::stringToResource($method->getResponseEmpty()));
        $this->assertEquals(901, $lib->getCode());
        $this->assertEquals('', $lib->getContent());
    }

    /**
     * @throws RequestException
     */
    public function testHeaders(): void
    {
        $method = new Answer\AnswerMock();
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

    /**
     * @throws RequestException
     */
    public function testChunked(): void
    {
        $method = new Answer\AnswerMock();
        $lib = $this->prepareSimple($method->getResponseChunked());
        $this->assertEquals(903, $lib->getCode());
        $this->assertEquals("Wikipedia in\r\n\r\nchunks.", $lib->getContent());
        $this->assertEquals('text/html', $lib->getHeader('Content-Type'));
        $this->assertEquals('chunked', $lib->getHeader('Transfer-Encoding'));
    }

    /**
     * @throws RequestException
     */
    public function testDeflated(): void
    {
        $method = new Answer\AnswerMock();
        $lib = $this->prepareSimple($method->getResponseDeflated());
        $this->assertEquals(904, $lib->getCode());
        $this->assertEquals("abcdefghijklmnopqrstuvwxyz012456789", $lib->getContent());
        $this->assertEquals('text/plain', $lib->getHeader('Content-Type'));
        $this->assertEquals('deflate', $lib->getHeader('Content-Encoding'));
    }

    /**
     * @throws RequestException
     */
    public function testLargeHeader(): void
    {
        $method = new Answer\AnswerMock();
        $this->expectException(RequestException::class);
        $this->prepareSimple(CommonTestClass::stringToResource($method->getResponseLargeHeader()));
    }

    /**
     * @throws RequestException
     */
    public function testAuthBasic(): void
    {
        $method = new Answer\AnswerMock();
        $lib = (new Http\Answer\AuthDigest())->setResponse($method->getResponseAuthDigest());
        $lib->processContent();
        $this->assertEquals(401, $lib->getCode());
        $this->assertEquals('Digest', $lib->getAuthType());
        $this->assertEquals('testrealm@host.com', $lib->getAuthRealm());
        $this->assertEquals(['auth', 'auth-int'], $lib->getQualitiesOfProtection());
        $this->assertEquals('dcd98b7102dd2f0e8b11d0f600bfb0c093', $lib->getRemoteRandomNumber());
        $this->assertEquals('5ccc069c403ebaf9f0171e9517f40e41', $lib->getDataToReturn());
        $this->assertEquals('md5', $lib->getAlgorithm());
    }

    /**
     * @throws RequestException
     */
    public function testAuthString(): void
    {
        $method = new Answer\AnswerMock();
        $lib = (new Answer\XAuthDigest())->setResponse($method->getResponseAuthDigest());
        $lib->processContent();
        $this->assertEquals(401, $lib->getCode());
        $this->assertEquals('Digest', $lib->getAuthType());
        $this->assertEquals('testrealm@host.com', $lib->getAuthRealm());
        $this->assertEquals(['auth', 'auth-int'], $lib->getQualitiesOfProtection());
        $this->assertEquals('dcd98b7102dd2f0e8b11d0f600bfb0c093', $lib->getRemoteRandomNumber());
        $this->assertEquals('5ccc069c403ebaf9f0171e9517f40e41', $lib->getDataToReturn());
        $this->assertEquals('md5', $lib->getAlgorithm());
    }

    /**
     * @throws RequestException
     */
    public function testAuthStream(): void
    {
        $method = new Answer\AnswerMock();
        $lib = (new Answer\XAuthDigest())->setResponse(CommonTestClass::stringToResource($method->getResponseAuthDigest()));
        $lib->processContent();
        $this->assertEquals(401, $lib->getCode());
        $this->assertEquals('Digest', $lib->getAuthType());
        $this->assertEquals('testrealm@host.com', $lib->getAuthRealm());
        $this->assertEquals(['auth', 'auth-int'], $lib->getQualitiesOfProtection());
        $this->assertEquals('dcd98b7102dd2f0e8b11d0f600bfb0c093', $lib->getRemoteRandomNumber());
        $this->assertEquals('5ccc069c403ebaf9f0171e9517f40e41', $lib->getDataToReturn());
        $this->assertEquals('md5', $lib->getAlgorithm());
    }

    /**
     * @param resource|string|null $content
     * @throws RequestException
     * @return Http\Answer
     */
    protected function prepareSimple($content): Http\Answer
    {
        $lib = new Answer\XAnswer(new Translations());
        $lib->addStringDecoding(new Http\Answer\DecodeStrings\Chunked());
        $lib->addStringDecoding(new Http\Answer\DecodeStrings\Deflated());
        return $lib->setResponse($content);
    }
}
