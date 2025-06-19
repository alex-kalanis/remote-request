<?php

namespace tests\ProtocolsTests\WebDav;


use kalanis\RemoteRequest\Protocols;


class QueryMock extends Protocols\WebDAV\Query
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
