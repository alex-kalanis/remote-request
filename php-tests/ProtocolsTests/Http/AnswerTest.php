<?php

namespace ProtocolsTests\Http;


use CommonTestClass;
use kalanis\RemoteRequest\Connection;
use kalanis\RemoteRequest\Protocols\Http;
use kalanis\RemoteRequest\Translations;
use kalanis\RemoteRequest\RequestException;


class AnswerMock extends Connection\Processor
{
    public function getResponseSimple()
    {
        return 'HTTP/0.1 900 KO' . Http::DELIMITER . Http::DELIMITER . 'abcdefghijkl';
    }

    public function getResponseEmpty()
    {
        return 'HTTP/0.1 901 KO';
    }

    public function getResponseHeaders()
    {
        return 'HTTP/0.1 902 KO' . Http::DELIMITER
            . 'Server: PhpUnit/9.3.0' . Http::DELIMITER
            . 'Content-Length: 12' . Http::DELIMITER
            . 'Content-Type: text/plain' . Http::DELIMITER
            . 'Connection: Closed' . Http::DELIMITER
            . Http::DELIMITER
            . 'abcdefghijkl'
        ;
    }

    public function getResponseChunked()
    {
        return 'HTTP/0.1 903 KO' . Http::DELIMITER
            . 'Server: PhpUnit/9.3.0' . Http::DELIMITER
            . 'Content-Length: 43' . Http::DELIMITER
            . 'Content-Type: text/html' . Http::DELIMITER
            . 'Transfer-Encoding: chunked' . Http::DELIMITER
            . 'Connection: Closed' . Http::DELIMITER
            . Http::DELIMITER
            . "4\r\nWiki\r\n5\r\npedia\r\nE\r\n in\r\n\r\nchunks.\r\n0\r\n\r\n"
        ;
    }

    public function getResponseDeflated()
    {
        return 'HTTP/0.1 904 KO' . Http::DELIMITER
            . 'Server: PhpUnit/9.3.0' . Http::DELIMITER
            . 'Content-Length: 37' . Http::DELIMITER
            . 'Content-Type: text/plain' . Http::DELIMITER
            . 'Content-Encoding: deflate' . Http::DELIMITER
            . 'Connection: Closed' . Http::DELIMITER
            . Http::DELIMITER
            . base64_decode("S0xKTklNS8/IzMrOyc3LLygsKi4pLSuvqKwyMDQyMTUzt7AEAA==")
        ;
    }

    public function getResponseLargeHeader()
    {
        return 'HTTP/0.1 904 KO' . Http::DELIMITER
            . 'Server: PhpUnit/9.3.0' . Http::DELIMITER
            . 'Content-Length: 0' . Http::DELIMITER
            . 'Content-Type: text/plain' . Http::DELIMITER
            . 'Content-Encoding: deflate' . Http::DELIMITER
            . 'Server: PhpUnit/9.3.0' . Http::DELIMITER
            . 'Content-Length: 0' . Http::DELIMITER
            . 'Content-Type: text/plain' . Http::DELIMITER
            . 'Content-Encoding: deflate' . Http::DELIMITER
            . 'Connection: Closed'
        ;
    }

    /**
     * @return string
     * @link https://en.wikipedia.org/wiki/Digest_access_authentication
     */
    public function getResponseAuthDigest()
    {
        return 'HTTP/0.1 401 Unauthorized' . Http::DELIMITER
            . 'Server: PhpUnit/9.3.0' . Http::DELIMITER
            . 'Date: Sun, 10 Apr 2022 20:26:47 GMT' . Http::DELIMITER
            . 'WWW-Authenticate: Digest realm="testrealm@host.com", qop="auth,auth-int", nonce="dcd98b7102dd2f0e8b11d0f600bfb0c093", opaque="5ccc069c403ebaf9f0171e9517f40e41"' . Http::DELIMITER
            . 'Content-Type: text/html' . Http::DELIMITER
            . 'Content-Length: 153' . Http::DELIMITER
            . Http::DELIMITER
            . '<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>Error</title>
  </head>
  <body>
    <h1>401 Unauthorized.</h1>
  </body>
</html>'
        ;
    }
}


class AnswerTest extends CommonTestClass
{
    /**
     * @throws RequestException
     */
    public function testSimple(): void
    {
        $method = new AnswerMock(new Translations());
        $lib = $this->prepareSimple($method->getResponseSimple());
        $this->assertEquals(900, $lib->getCode());
        $this->assertEquals('abcdefghijkl', $lib->getContent());
    }

    /**
     * @throws RequestException
     */
    public function testSimpleStream(): void
    {
        $method = new AnswerMock(new Translations());
        $lib = $this->prepareSimple(CommonTestClass::stringToResource($method->getResponseSimple()));
        $this->assertEquals(900, $lib->getCode());
        $this->assertEquals('abcdefghijkl', $lib->getContent());
    }

    /**
     * @throws RequestException
     */
    public function testEmpty(): void
    {
        $method = new AnswerMock(new Translations());
        $lib = $this->prepareSimple($method->getResponseEmpty());
        $this->assertEquals(901, $lib->getCode());
        $this->assertEquals('', $lib->getContent());
    }

    /**
     * @throws RequestException
     */
    public function testEmptyStream(): void
    {
        $method = new AnswerMock(new Translations());
        $lib = $this->prepareSimple(CommonTestClass::stringToResource($method->getResponseEmpty()));
        $this->assertEquals(901, $lib->getCode());
        $this->assertEquals('', $lib->getContent());
    }

    /**
     * @throws RequestException
     */
    public function testHeaders(): void
    {
        $method = new AnswerMock(new Translations());
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
        $method = new AnswerMock(new Translations());
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
        $method = new AnswerMock(new Translations());
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
        $method = new AnswerMock(new Translations());
        $this->expectException(RequestException::class);
        $this->prepareSimple(CommonTestClass::stringToResource($method->getResponseLargeHeader()));
    }

    /**
     * @throws RequestException
     */
    public function testAuthBasic(): void
    {
        $method = new AnswerMock(new Translations());
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
        $method = new AnswerMock(new Translations());
        $lib = (new XAuthDigest())->setResponse($method->getResponseAuthDigest());
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
        $method = new AnswerMock(new Translations());
        $lib = (new XAuthDigest())->setResponse(CommonTestClass::stringToResource($method->getResponseAuthDigest()));
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
     * @param string|resource $content
     * @return Http\Answer
     * @throws RequestException
     */
    protected function prepareSimple($content): Http\Answer
    {
        $lib = new XAnswer();
        $lib->addStringDecoding(new Http\Answer\DecodeStrings\Chunked());
        $lib->addStringDecoding(new Http\Answer\DecodeStrings\Deflated());
        return $lib->setResponse($content);
    }
}


class XAnswer extends Http\Answer
{
    protected $seekSize = 20; // in how big block we will look for delimiters
    protected $seekPos = 15; // must be reasonably lower than seekSize - because it's necessary to find delimiters even on edges
    protected $maxHeaderSize = 200; // die early in stream
    protected $maxStringSize = 100; // pass into stream
}


class XAuthDigest extends Http\Answer\AuthDigest
{
    protected $maxStringSize = 100; // pass into stream
}
