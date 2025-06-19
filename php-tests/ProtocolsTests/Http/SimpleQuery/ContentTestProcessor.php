<?php

namespace tests\ProtocolsTests\Http\SimpleQuery;


use tests\CommonTestClass;
use kalanis\RemoteRequest\Connection;
use kalanis\RemoteRequest\Protocols\Http;
use kalanis\RemoteRequest\RequestException;


class ContentTestProcessor extends TestProcessor
{
    public function process(): Connection\Processor
    {
        return $this;
    }

    /**
     * @throws RequestException
     * @return resource|null
     */
    public function getResponse()
    {
        return CommonTestClass::stringToResource('HTTP/0.1 901 KO' . Http::DELIMITER . Http::DELIMITER . 'abcdefghijkl');
    }
}
