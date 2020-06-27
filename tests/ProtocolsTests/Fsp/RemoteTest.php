<?php

namespace ProtocolsTests\Fsp;

use CommonTestClass;
use RemoteRequest\Connection;
use RemoteRequest\Pointers;
use RemoteRequest\Protocols\Fsp;
use RemoteRequest\Schemas;

class RemoteTest extends CommonTestClass
{
    /**
     * Direct call for testing from CLI; just comment that return
     * ./phpunit --testsuite FspRemote
     * @throws \RemoteRequest\RequestException
     * @medium
     */
    public function testRound(): void
    {
        $this->assertTrue(true);
        return;

        $wrapper = Schemas\ASchema::getSchema(Schemas\ASchema::SCHEMA_UDP);
//        $wrapper->setTarget('ftp.vslib.cz', 21);
//        $wrapper->setTarget('www.720k.net', 21, 60);
//        $wrapper->setTarget('fsp.720k.net', 21, 60);
        $wrapper->setTarget('10.0.0.30', 54321, 10);
        $processor = new Connection\Processor(new Pointers\Socket());
        $query = new Fsp\Query();
        $answer = new Fsp\Answer();
        $version = new Fsp\Query\Version($query);
        $version->setKey(75)->setSequence(92)->compile();

        $result = Fsp\Answer\AnswerFactory::getObject(
            $answer->setResponse(
                $processor->setProtocolSchema($wrapper)->setData($query)->getResponse()
            )->process()
        );
var_dump($result);
    }
}