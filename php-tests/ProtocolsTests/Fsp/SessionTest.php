<?php

namespace tests\ProtocolsTests\Fsp;


use tests\CommonTestClass;
use kalanis\RemoteRequest\RequestException;


class SessionTest extends CommonTestClass
{
    /**
     * @throws RequestException
     */
    public function testSeqPass(): void
    {
        $mock = Session\SequenceMock::newSequence();
        $this->assertEquals(75, $mock->getKey());
        $mock->checkSequence(75);
        $mock->updateSequence();
    }

    /**
     * @throws RequestException
     */
    public function testSeqFail(): void
    {
        $mock = new Session\SequenceMock();
        $this->expectException(RequestException::class);
        $mock->checkSequence(75);
    }

    /**
     * @throws RequestException
     */
    public function testKeyNone(): void
    {
        $mock = new Session\SessionMock();
        $this->assertFalse($mock->hasKey());
    }

    /**
     * @throws RequestException
     */
    public function testKeyFail(): void
    {
        $mock = new Session\SessionMock();
        $this->expectException(RequestException::class);
        $mock->getKey();
    }

    /**
     * @throws RequestException
     */
    public function testSequenceFail(): void
    {
        $mock = new Session\SessionMock();
        $this->expectException(RequestException::class);
        $mock->getSequence();
    }

    public function testKeyNotFound(): void
    {
        $mock = new Session\SessionMock();
        $mock->setHost('asdf');
        $this->assertFalse($mock->hasKey());
    }

    /**
     * @throws RequestException
     */
    public function testKeyFound(): void
    {
        $mock = new Session\SessionMock();
        $mock->setHost('asdf');
        $this->assertEquals(64, $mock->getKey());
        $mock->setKey(37);
        $this->assertEquals(37, $mock->getKey());
        $mock->clear();
        $mock->clear();
    }

    /**
     * @throws RequestException
     */
    public function testSequenceFound(): void
    {
        $mock = new Session\SessionMock();
        $mock->clear();
        $mock->setHost('asdf');
        $this->assertEquals(75, $mock->getSequence());
        $mock->updateSequence(75);
        $mock->clear();

    }

    /**
     * @throws RequestException
     */
    public function testSequenceNotSet(): void
    {
        $mock = new Session\SessionMock();
        $mock->clear();
        $mock->setHost('asdf');
        $this->expectException(RequestException::class);
        $mock->updateSequence(94);
    }

    /**
     * @throws RequestException
     */
    public function testSequences(): void
    {
        $mock = new Session\SessionMock();
        $mock->clear();
        $mock->setHost('asdf');
        $this->assertEquals(75, $mock->getSequence());
        $mock->setHost('poiu');
        $this->assertEquals(75, $mock->getSequence());
        $mock->setHost('asdf');
        $mock->clear();
        $mock->setHost('poiu');
        $this->assertEquals(64, $mock->getKey());
        $mock->clear();
        $mock->clear();
    }
}
