<?php

namespace ProtocolsTests\Fsp;

use CommonTestClass;
use RemoteRequest\Connection;
use RemoteRequest\Protocols\Fsp;
use RemoteRequest\Schemas;
use RemoteRequest\Sockets;
use RemoteRequest\Wrappers;

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
        $processor = new Connection\Processor(new Sockets\Socket());
        $query = new Fsp\Query();
        $answer = new Fsp\Answer();
        $answer->canDump = true;
        $version = new Fsp\Query\Version($query);
        $version->setKey(32)->setSequence(16)->compile();

        $response = $processor->setProtocolSchema($wrapper)->setData($query)->getResponse();
        $result = Fsp\Answer\AnswerFactory::getObject(
            $answer->setResponse(
                $response
            )->process()
        )->process();

        /** @var Fsp\Answer\Version $result */
        $this->assertEquals('fspd 2.8.1b29', $result->getVersion());
        $this->assertFalse($result->isReadOnly());
    }

    public function testDirList(): void
    {
        $runner = new Fsp\Runner();
        $lib = new Wrappers\Fsp\Dir($runner);
        print_r($lib->stats('fsp://10.0.0.30:54321/deb/asyncio.pdf', 0));
        $lib->close();
   }
}