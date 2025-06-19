<?php

namespace tests\ProtocolsTests\Fsp;


use tests\CommonTestClass;
use kalanis\RemoteRequest\Protocols\Fsp;
use kalanis\RemoteRequest\RequestException;


class ProtocolTest extends CommonTestClass
{
    public function testQuerySimple(): void
    {
        $mock = new Protocol\ProtocolQueryMock();
        $lib = new Fsp\Query();
        $lib->setCommand(Fsp::CC_GET_DIR)
            ->setKey(258)
            ->setSequence(772)
            ->setFilePosition(0)
            ->setContent('DATA' . chr(0))
            ->setExtraData(chr(01) . chr(0));

        $this->assertEquals(stream_get_contents($mock->getRequestSimple()), $lib->getPacket());
    }

    public function testQueryFailChecksum(): void
    {
        $mock = new Protocol\ProtocolQueryMock();
        $lib = new Fsp\Query();
        $lib->setCommand(Fsp::CC_GET_DIR)
            ->setKey(258)
            ->setSequence(772)
            ->setFilePosition(0)
            ->setContent('DATA' . chr(0))
            ->setExtraData(chr(01) . chr(0));

        $this->assertNotEquals(stream_get_contents($mock->getRequestFailedChk()), $lib->getPacket());
    }

    /**
     * @throws RequestException
     */
    public function testAnswerSimple(): void
    {
        $lib = new Protocol\ProcessorMock();
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
        $lib = new Protocol\ProcessorMock();
        $read = new Fsp\Answer();
        $this->expectException(RequestException::class);
        $read->setResponse($lib->getResponseFailedChk())->process();
    }

    /**
     * @throws RequestException
     */
    public function testAnswerShort(): void
    {
        $mock = new Protocol\ProcessorMock();
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
        $mock = new Protocol\ProcessorMock();
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
        $lib = new Protocol\ProcessorMock();
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
