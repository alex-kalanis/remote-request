<?php

namespace ProtocolsTests\Fsp;


use CommonTestClass;
use kalanis\RemoteRequest\Connection;
use kalanis\RemoteRequest\Protocols\Fsp;
use kalanis\RemoteRequest\RequestException;


class ProtocolQueryMock
{
    /**
     * What we send into server
     * @return string
     */
    public function getRequestSimple(): string
    {
        return Common::makeDummyQuery([
            0x41, # CC_GET_DIR
            0x7f, # checksum
            0x01, 0x02, # key
            0x03, 0x04, # sequence
            0x00, 0x05, # data_length
            0x00, 0x00, 0x00, 0x00, # position
            'DATA', 0x00, # content "DATA"
            0x01, 0x00, # xtra
        ]);
    }
    /**
     * What we send into server
     * @return string
     */
    public function getRequestFailedChk(): string
    {
        return Common::makeDummyQuery([
            0x41, # CC_GET_DIR
            0xAC, # checksum
            0x01, 0x02, # key
            0x03, 0x04, # sequence
            0x00, 0x05, # data_length
            0x00, 0x00, 0x00, 0x00, # position
            'DATA', 0x00, # content "DATA"
            0x01, 0x00, # xtra
        ]);
    }
}

class ProcessorMock extends Connection\Processor
{
    /**
     * What server responds
     * @return string
     */
    public function getResponseSimple(): string
    {
        return Common::makeDummyQuery([
            0x42, # CC_GET_FILE
            0x8b, # checksum - 139
            0x01, 0x02, # key
            0x03, 0x04, # sequence
            0x00, 0x04, # data_length
            0x00, 0x00, 0x00, 0x20, # position
            'DATA', # content "DATA"
            # no xtra
        ]);
    }

    /**
     * What server responds
     * @return string
     */
    public function getResponseReal(): string
    {
        return Common::makeDummyQuery([
            0x10, # CC_VERSION
            0x1B, # checksum 27
            0xF5, 0x8C, # key
            0x00, 0x10, # sequence
            0x00, 0x0E, # data_length
            0x00, 0x00, 0x00, 0x03, # position
            0x66, 0x73, 0x70, 0x64, 0x20, 0x32, 0x2E, 0x38, 0x2E, 0x31, 0x62, 0x32, 0x39, 0x00, # content "fspd 2.8.1b29"
            0x21, 0x05, 0xAC, # xtra
        ]);
    }

    /**
     * What server should not respond
     * @return string
     */
    public function getResponseFailedChk(): string
    {
        return Common::makeDummyQuery([
            0x42, # CC_GET_FILE
            0x9A, # checksum - fail!
            0x01, 0x02, # key
            0x03, 0x04, # sequence
            0x00, 0x04, # data_length
            0x00, 0x00, 0x00, 0x20, # position
            'DATA', # content "DATA"
        ]);
    }

    public function getResponseShort(): string
    {
        return Common::makeDummyQuery([
            0x81, # CC_TEST
            0x31, # checksum
            0x01, 0x02, # key
        ]);
    }

    public function getResponseLong(): string
    {
        return Common::makeDummyQuery([
            0x81, # CC_TEST
            0x0B, # checksum
            0x01, 0x02, # key
            0x03, 0x04, # sequence
            0xff, 0xff, # data_length
            0x00, 0x00, 0x00, 0x00, # position
            str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', 29), # shitty content
            # no extra data
        ]);
    }
}


class ProtocolTest extends CommonTestClass
{
    public function testQuerySimple(): void
    {
        $mock = new ProtocolQueryMock();
        $lib = new Fsp\Query();
        $lib->setCommand(Fsp::CC_GET_DIR)
            ->setKey(258)
            ->setSequence(772)
            ->setFilePosition(0)
            ->setContent('DATA' . chr(0))
            ->setExtraData(chr(01) . chr(0));

        $this->assertEquals($mock->getRequestSimple(), $lib->getPacket());
    }

    public function testQueryFailChecksum(): void
    {
        $mock = new ProtocolQueryMock();
        $lib = new Fsp\Query();
        $lib->setCommand(Fsp::CC_GET_DIR)
            ->setKey(258)
            ->setSequence(772)
            ->setFilePosition(0)
            ->setContent('DATA' . chr(0))
            ->setExtraData(chr(01) . chr(0));

        $this->assertNotEquals($mock->getRequestFailedChk(), $lib->getPacket());
    }

    /**
     * @throws RequestException
     */
    public function testAnswerSimple(): void
    {
        $lib = new ProcessorMock();
        $read = new Fsp\Answer();
//        $read->canDump = true;
        $read->setResponse($lib->getResponseSimple())->process();
        $this->assertEquals(Fsp::CC_GET_FILE, $read->getCommand());
        $this->assertEquals(258, $read->getKey());
        $this->assertEquals(772, $read->getSequence());
        $this->assertEquals(32, $read->getFilePosition());
        $this->assertEquals('DATA', $read->getContent());
    }

    /**
     * @throws RequestException
     */
    public function testAnswerFailChecksumSimple(): void
    {
        $lib = new ProcessorMock();
        $read = new Fsp\Answer();
        $this->expectException(RequestException::class);
        $read->setResponse($lib->getResponseFailedChk())->process();
    }

    /**
     * @throws RequestException
     */
    public function testAnswerShort(): void
    {
        $mock = new ProcessorMock();
        $read = new Fsp\Answer();
        $this->expectException(RequestException::class);
        $read->setResponse($mock->getResponseShort())->process();
        Fsp\Answer\AnswerFactory::getObject($read)->process();
        $this->expectExceptionMessageMatches('Response too short');
    }

    /**
     * @throws RequestException
     */
    public function testAnswerLong(): void
    {
        $mock = new ProcessorMock();
        $read = new Fsp\Answer();
        $this->expectException(RequestException::class);
        $read->setResponse($mock->getResponseLong())->process();
        Fsp\Answer\AnswerFactory::getObject($read)->process();
        $this->expectExceptionMessageMatches('Response too large');
    }

    /**
     * @throws RequestException
     * fails on real hash - need to calculate it "by hand"!
     */
    public function testAnswerReal(): void
    {
        $lib = new ProcessorMock();
        $read = new Fsp\Answer();
//        $read->canDump = true;
        $read->setResponse($lib->getResponseReal())->process();
        $this->assertEquals(Fsp::CC_VERSION, $read->getCommand());
        $this->assertEquals(62860, $read->getKey());
        $this->assertEquals(16, $read->getSequence());
        $this->assertEquals(3, $read->getFilePosition()); // 3 at extra
        $this->assertEquals('fspd 2.8.1b29' . chr(0), $read->getContent());
    }
}
