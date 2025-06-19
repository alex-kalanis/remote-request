<?php

namespace tests\ProtocolsTests\Http\Answer;


use kalanis\RemoteRequest\Connection;
use kalanis\RemoteRequest\Protocols\Http;


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
            . 'Server: PhpUnit/9.3.0' . Http::DELIMITER
            . 'Content-Length: 12' . Http::DELIMITER
            . 'Content-Type: text/plain' . Http::DELIMITER
            . 'Connection: Closed' . Http::DELIMITER
            . Http::DELIMITER
            . 'abcdefghijkl'
        ;
    }

    public function getResponseChunked(): string
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

    public function getResponseDeflated(): string
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

    public function getResponseLargeHeader(): string
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
    public function getResponseAuthDigest(): string
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
