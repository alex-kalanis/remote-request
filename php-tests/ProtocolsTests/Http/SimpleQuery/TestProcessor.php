<?php

namespace tests\ProtocolsTests\Http\SimpleQuery;


use tests\CommonTestClass;
use kalanis\RemoteRequest\Connection;
use kalanis\RemoteRequest\Protocols\Http;
use kalanis\RemoteRequest\RequestException;


class TestProcessor extends Connection\Processor
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
        return CommonTestClass::stringToResource('HTTP/0.1 900 KO' . Http::DELIMITER);
    }
}
