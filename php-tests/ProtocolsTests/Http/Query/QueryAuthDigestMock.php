<?php

namespace tests\ProtocolsTests\Http\Query;


use kalanis\RemoteRequest\Protocols\Http;


class QueryAuthDigestMock extends Http\Query\AuthDigest
{
    /**
     * Overwrite because random string in testing does not work
     * @return string
     */
    protected function generateBoundary(): string
    {
        return '--PHPFSock--';
    }

    protected function getRandomString(): string
    {
        return '0a4f113b';
    }
}
