<?php

use RemoteRequest\Connection;
use RemoteRequest\Protocols\Fsp;
use RemoteRequest\Wrappers;

class FspProtocolQueryMock
{
    /**
     * What we send into server
     * @return string
     */
    public function getRequestSimple(): string
    {
        return fspMakeDummyQuery([
            0x41, # CC_GET_DIR
            0x7f, # checksum
            0x01, 0x02, # key
            0x03, 0x04, # sequence
            0x00, 0x05, # data_length
            0x00, 0x00, 0x00, 0x00, # position
            0x44, 0x41, 0x54, 0x41, 0x00, # content "DATA"
            0x01, 0x00, # xtra
        ]);
    }
}

class FspProcessorMock extends Connection\Processor
{
    /**
     * What server responds
     * @return string
     */
    public function getResponseSimple(): string
    {
        return fspMakeDummyQuery([
            0x42, # CC_GET_FILE
            0x8b, # checksum - 139
            0x01, 0x02, # key
            0x03, 0x04, # sequence
            0x00, 0x04, # data_length
            0x00, 0x00, 0x00, 0x20, # position
            0x44, 0x41, 0x54, 0x41, # content "DATA"
        ]);
    }

    /**
     * What server should not respond
     * @return string
     */
    public function getResponseFailedChk(): string
    {
        return fspMakeDummyQuery([
            0x42, # CC_GET_FILE
            0x9A, # checksum - fail!
            0x01, 0x02, # key
            0x03, 0x04, # sequence
            0x00, 0x04, # data_length
            0x00, 0x00, 0x00, 0x20, # position
            0x44, 0x41, 0x54, 0x41, # content "DATA"
        ]);
    }
}

class FspProtocolTest extends CommonTestClass
{
    public function testQuerySimple()
    {
        $mock = new FspProtocolQueryMock();
        $lib = new Fsp\Query();
        $lib->setCommand(RemoteRequest\Protocols\Fsp::CC_GET_DIR)
            ->setKey(258)
            ->setSequence(772)
            ->setFilePosition(0)
            ->setData('DATA' . chr(0))
            ->setExtraData(chr(01) . chr(0));

        $this->assertEquals($mock->getRequestSimple(), $lib->getData());
    }

    /**
     * @throws \RemoteRequest\RequestException
     */
    public function testAnswerSimple()
    {
        $lib = new FspProcessorMock();
        $read = new Fsp\Answer();
        $read->setResponse($lib->getResponseSimple())->process();
        $this->assertEquals(RemoteRequest\Protocols\Fsp::CC_GET_FILE, $read->getCommand());
        $this->assertEquals(258, $read->getKey());
        $this->assertEquals(772, $read->getSequence());
        $this->assertEquals(32, $read->getFilePosition());
        $this->assertEquals('DATA', $read->getContent());
    }

    /**
     * @expectedException \RemoteRequest\RequestException
     */
    public function testAnswerFailChecksumSimple()
    {
        $lib = new FspProcessorMock();
        $read = new Fsp\Answer();
        $read->setResponse($lib->getResponseFailedChk())->process();
    }

    /**
     * Direct call for testing from CLI; just comment that return
     * ./phpunit --filter FspProtocolTest::testRemoteRound
     * @throws \RemoteRequest\RequestException
     */
    public function testRemoteRound()
    {
        $this->assertTrue(true);
        return;

        $wrapper = Wrappers\AWrapper::getWrapper(Wrappers\AWrapper::SCHEMA_UDP);
//        $wrapper->setTarget('ftp.vslib.cz', 21);
//        $wrapper->setTarget('www.720k.net', 21, 60);
//        $wrapper->setTarget('fsp.720k.net', 21, 60);
        $wrapper->setTarget('10.0.0.30', 54321, 10);
        $processor = new RemoteRequest\Connection\Processor(new RemoteRequest\Pointers\Socket());
        $query = new Fsp\Query();
        $answer = new Fsp\Answer();
        $version = new Fsp\Query\Version($query);
        $version->setKey(75)->setSequence(92)->compile();

        $result = Fsp\Answer\AnswerFactory::getObject(
            $answer->setResponse(
                $processor->setProtocolWrapper($wrapper)->setData($query)->getResponse()
            )->process()
        );
var_dump($result);
    }
}