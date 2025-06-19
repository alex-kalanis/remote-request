<?php

namespace tests\ProtocolsTests\Http\Query;


use kalanis\RemoteRequest\Protocols\Http;


class QueryAuthBasicMock extends Http\Query\AuthBasic
{
    /**
     * Overwrite because random string in testing does not work
     * @return string
     */
    protected function generateBoundary(): string
    {
        return '--PHPFSock--';
    }
}
