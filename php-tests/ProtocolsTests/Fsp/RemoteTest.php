<?php

namespace ProtocolsTests\Fsp;


use CommonTestClass;
use kalanis\RemoteRequest\Connection;
use kalanis\RemoteRequest\Interfaces;
use kalanis\RemoteRequest\Protocols\Fsp;
use kalanis\RemoteRequest\Sockets;
use kalanis\RemoteRequest\Translations;
use kalanis\RemoteRequest\Wrappers;


class RemoteTest extends CommonTestClass
{
    /**
     * Direct call for testing from CLI; just comment that return
     * ./phpunit --testsuite FspRemote
     * @throws \kalanis\RemoteRequest\RequestException
     * @medium
     */
    public function testRound(): void
    {
        $this->assertTrue(true);
        return;

        $lang = new Translations();
        $params = Connection\Params\Factory::getForSchema(Interfaces\ISchema::SCHEMA_UDP, $lang);
//        $params->setTarget('ftp.vslib.cz', 21);
//        $params->setTarget('www.720k.net', 21, 60);
//        $params->setTarget('fsp.720k.net', 21, 60);
        $params->setTarget('10.0.0.30', 54321, 10);
        $processor = new Connection\Processor(new Sockets\Socket());
        $query = new Fsp\Query();
        $answer = new Fsp\Answer();
        $answer->canDump = true;
        $version = new Fsp\Query\Version($query);
        $version->setKey(32)->setSequence(16)->compile();

        $response = $processor->setConnectionParams($params)->setData($query)->process()->getResponse();
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
        Wrappers\Fsp::register();
        print_r(['stats', stat('fsp://10.0.0.30:54321/deb/asyncio.pdf')]);
    }
}
