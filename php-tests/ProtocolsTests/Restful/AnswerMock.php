<?php

namespace tests\ProtocolsTests\Restful;


use tests\CommonTestClass;
use kalanis\RemoteRequest\Connection;
use kalanis\RemoteRequest\Protocols\Http;


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
