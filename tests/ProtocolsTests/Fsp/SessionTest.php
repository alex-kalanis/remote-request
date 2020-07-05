<?php

namespace ProtocolsTests\Fsp;

use CommonTestClass;
use RemoteRequest\Protocols\Fsp;

class SequenceMock extends Fsp\Session\Sequence
{
    protected function getRandInitial(): int
    {
        return 75;
    }
}

class SessionMock extends Fsp\Session
{
    protected function getRandInitial(): int
    {
        return 64;
    }

    protected function sequencer($withInit = true): Fsp\Session\Sequence
    {
        $lib = new SequenceMock();
        if ($withInit) {
            $lib->generateSequence();
        }
        return $lib;
    }
}

class SessionTest extends CommonTestClass
{
    /**
     * @throws \RemoteRequest\RequestException
     */
    public function testSeqPass(): void
    {
        $mock = SequenceMock::newSequence();
        $this->assertEquals(75, $mock->getKey());
        $mock->checkSequence(75);
        $mock->updateSequence();
    }

    /**
     * @expectedException \RemoteRequest\RequestException
     */
    public function testSeqFail(): void
    {
        $mock = new SequenceMock();
        $mock->checkSequence(75);
    }

    /**
     * @throws \RemoteRequest\RequestException
     */
    public function testKeyNone(): void
    {
        $mock = new SessionMock();
        $this->assertFalse($mock->hasKey());
    }

    /**
     * @expectedException \RemoteRequest\RequestException
     */
    public function testKeyFail(): void
    {
        $mock = new SessionMock();
        $mock->getKey();
    }

    /**
     * @expectedException \RemoteRequest\RequestException
     */
    public function testSequenceFail(): void
    {
        $mock = new SessionMock();
        $mock->getSequence();
    }

    /**
     * @throws \RemoteRequest\RequestException
     */
    public function testKeyNotFound(): void
    {
        $mock = new SessionMock();
        $mock->setHost('asdf');
        $this->assertFalse($mock->hasKey());
    }

    /**
     * @throws \RemoteRequest\RequestException
     */
    public function testKeyFound(): void
    {
        $mock = new SessionMock();
        $mock->setHost('asdf');
        $this->assertEquals(64, $mock->getKey());
        $mock->setKey(37);
        $this->assertEquals(37, $mock->getKey());
        $mock->clear();
        $mock->clear();
    }

    /**
     * @throws \RemoteRequest\RequestException
     */
    public function testSequenceFound(): void
    {
        $mock = new SessionMock();
        $mock->clear();
        $mock->setHost('asdf');
        $this->assertEquals(75, $mock->getSequence());
        $mock->setSequence(75);
        $mock->clear();

    }

    /**
     * @expectedException \RemoteRequest\RequestException
     */
    public function testSequenceNotSet(): void
    {
        $mock = new SessionMock();
        $mock->clear();
        $mock->setHost('asdf');
        $mock->setSequence(94);
    }

    /**
     * @throws \RemoteRequest\RequestException
     */
    public function testSequences(): void
    {
        $mock = new SessionMock();
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