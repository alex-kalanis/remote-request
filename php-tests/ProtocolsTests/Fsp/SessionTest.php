<?php

namespace ProtocolsTests\Fsp;


use CommonTestClass;
use kalanis\RemoteRequest\Protocols\Fsp;
use kalanis\RemoteRequest\RequestException;
use kalanis\RemoteRequest\Translations;


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
        $lib = new SequenceMock($this->lang);
        if ($withInit) {
            $lib->generateSequence();
        }
        return $lib;
    }
}


class SessionTest extends CommonTestClass
{
    /**
     * @throws RequestException
     */
    public function testSeqPass(): void
    {
        $mock = SequenceMock::newSequence(new Translations());
        $this->assertEquals(75, $mock->getKey());
        $mock->checkSequence(75);
        $mock->updateSequence();
    }

    /**
     * @throws RequestException
     */
    public function testSeqFail(): void
    {
        $mock = new SequenceMock(new Translations());
        $this->expectException(RequestException::class);
        $mock->checkSequence(75);
    }

    /**
     * @throws RequestException
     */
    public function testKeyNone(): void
    {
        $mock = new SessionMock(new Translations());
        $this->assertFalse($mock->hasKey());
    }

    /**
     * @throws RequestException
     */
    public function testKeyFail(): void
    {
        $mock = new SessionMock(new Translations());
        $this->expectException(RequestException::class);
        $mock->getKey();
    }

    /**
     * @throws RequestException
     */
    public function testSequenceFail(): void
    {
        $mock = new SessionMock(new Translations());
        $this->expectException(RequestException::class);
        $mock->getSequence();
    }

    /**
     * @throws RequestException
     */
    public function testKeyNotFound(): void
    {
        $mock = new SessionMock(new Translations());
        $mock->setHost('asdf');
        $this->assertFalse($mock->hasKey());
    }

    /**
     * @throws RequestException
     */
    public function testKeyFound(): void
    {
        $mock = new SessionMock(new Translations());
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
        $mock = new SessionMock(new Translations());
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
        $mock = new SessionMock(new Translations());
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
        $mock = new SessionMock(new Translations());
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
