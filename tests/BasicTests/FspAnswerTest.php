<?php

use RemoteRequest\Connection;
use RemoteRequest\Protocols\Fsp;

class FspAnswerMock extends Connection\Processor
{
    public function getResponseVersion(): string
    {
        return fspMakeDummyQuery([
            0x10, # CC_VERSION
            0x31, # checksum
            0x01, 0x02, # key
            0x03, 0x04, # sequence
            0x00, 0x0A, # data_length
            0x00, 0x00, 0x00, 0x01, # position
            'Test v0.1', 0x00, # content
            0b01000100, # extra data - settings
        ]);
    }

    public function getResponseVersionPayload(): string
    {
        return fspMakeDummyQuery([
            0x10, # CC_VERSION
            0x94, # checksum
            0x01, 0x02, # key
            0x03, 0x04, # sequence
            0x00, 0x0A, # data_length
            0x00, 0x00, 0x00, 0x05, # position
            'Test v0.2', 0x00, # content
            0b10011100, 0x00, 0x00, 0x04, 0x00, 0x02, 0x00 # extra data - settings
        ]);
    }

    public function getResponseDir(): string
    {
        return fspMakeDummyQuery([
            0x41, # CC_GET_DIR
            0x2E, # checksum
            0x01, 0x02, # key
            0x03, 0x04, # sequence
            0x00, 0x19, # data_length
            0x00, 0x00, 0x00, 0x0C, # position
            # content...
            0x12, 0x34, 0x56, 0x78, # time
            0x00, 0x00, 0x04, 0x00, # size
            0x01,                   # type
            'foo-bar-baz.txt', 0x00 # filename - "foo-bar-baz\0"
        ]);
    }

    public function getResponseError(): string
    {
        return fspMakeDummyQuery([
            0x40, # CC_ERR
            0xD8, # checksum
            0x01, 0x02, # key
            0x03, 0x04, # sequence
            0x00, 0x10, # data_length
            0x00, 0x00, 0x00, 0x00, # position
            'Testing error 1', 0x00, # content
             # extra data - empty
        ]);
    }

    public function getResponseErrorDetails(): string
    {
        return fspMakeDummyQuery([
            0x40, # CC_ERR
            0x19, # checksum
            0x01, 0x02, # key
            0x03, 0x04, # sequence
            0x00, 0x10, # data_length
            0x00, 0x00, 0x00, 0x00, # position
            'Testing error 2', 0x00, # content
            0x3F, # extra data - code 63 - "?"
        ]);
    }

    public function getResponseTest(): string
    {
        return fspMakeDummyQuery([
            0x81, # CC_TEST
            0x0B, # checksum
            0x01, 0x02, # key
            0x03, 0x04, # sequence
            0x00, 0x00, # data_length
            0x00, 0x00, 0x00, 0x00, # position
            # no content
            # no extra data
        ]);
    }

    public function getResponseNothing(): string
    {
        return fspMakeDummyQuery([
            0x4A, # CC_BYE
            0x54, # checksum
            0x01, 0x02, # key
            0x03, 0x04, # sequence
            0x00, 0x00, # data_length
            0x00, 0x00, 0x00, 0x00, # position
            # no content
            # no extra data
        ]);
    }

    public function getResponseGetFile(): string
    {
        return fspMakeDummyQuery([
            0x42, # CC_GET_FILE
            0x4D, # checksum
            0x01, 0x02, # key
            0x03, 0x04, # sequence
            0x00, 0x0F, # data_length
            0x00, 0x00, 0x04, 0x00, # position
            'Testing data 1', 0x00, # content
            # no extra data
        ]);
    }

    public function getResponseUpload(): string
    {
        return fspMakeDummyQuery([
            0x43, # CC_UP_LOAD
            0x4F, # checksum
            0x01, 0x02, # key
            0x03, 0x04, # sequence
            0x00, 0x00, # data_length
            0x00, 0x00, 0x02, 0x00, # position
            # no content
            # no extra data
        ]);
    }

    public function getResponseProtection(): string
    {
        return fspMakeDummyQuery([
            0x47, # CC_GET_PRO
            0xEB, # checksum
            0x01, 0x02, # key
            0x03, 0x04, # sequence
            0x00, 0x08, # data_length
            0x00, 0x00, 0x00, 0x01, # position
            'foo/bar', 0x00, # content - directory "foo/bar"
            0b11100110, # no extra data
        ]);
    }

    public function getResponseStats(): string
    {
        return fspMakeDummyQuery([
            0x4D, # CC_STATS
            0x34, # checksum
            0x01, 0x02, # key
            0x03, 0x04, # sequence
            0x00, 0x09, # data_length
            0x00, 0x00, 0x00, 0x00, # position
            0x5E, 0x0B, 0xE1, 0x01,    0x00, 0x23, 0x44, 0x00,    0b00100000, # file stats - 2020-01-01T00:00:01+00:00, 2311168 B file
            # no extra data
        ]);
    }
}

class FspAnswerTest extends CommonTestClass
{
    /**
     * @throws \RemoteRequest\RequestException
     */
    public function testAnswerVersion()
    {
        $mock = new FspAnswerMock();
        $read = new Fsp\Answer();
//        $read->canDump = true;
        $read->setResponse($mock->getResponseVersion())->process();
        $process = Fsp\Answer\AnswerFactory::getObject($read)->process();
        /** @var Fsp\Answer\Version $process */
        $this->assertEquals(258, $process->getDataClass()->getKey());
        $this->assertEquals(772, $process->getDataClass()->getSequence());
        $this->assertEquals('Test v0.1', $process->getVersion());
        $this->assertFalse($process->isServerLogging());
        $this->assertTrue($process->isReadOnly());
        $this->assertFalse($process->wantReverseLookup());
        $this->assertFalse($process->isPrivateMode());
        $this->assertTrue($process->acceptsExtra());
    }

    /**
     * @throws \RemoteRequest\RequestException
     */
    public function testAnswerVersionPayload()
    {
        $mock = new FspAnswerMock();
        $read = new Fsp\Answer();
        $read->setResponse($mock->getResponseVersionPayload())->process();
        $process = Fsp\Answer\AnswerFactory::getObject($read)->process();
        /** @var Fsp\Answer\Version $process */
        $this->assertEquals('Test v0.2', $process->getVersion());
        $this->assertTrue($process->isServerLogging());
        $this->assertFalse($process->isReadOnly());
        $this->assertFalse($process->wantReverseLookup());
        $this->assertTrue($process->isPrivateMode());
        $this->assertTrue($process->acceptsExtra());
        $this->assertTrue($process->canThruControl());
        $this->assertEquals(1024, $process->thruControlMaxAllowed());
        $this->assertEquals(512, $process->thruControlMaxPayload());
    }

    /**
     * @throws \RemoteRequest\RequestException
     */
    public function testAnswerDir()
    {
        $mock = new FspAnswerMock();
        $read = new Fsp\Answer();
        $read->setResponse($mock->getResponseDir())->process();
        $process = Fsp\Answer\AnswerFactory::getObject($read)->process();
        /** @var Fsp\Answer\GetDir $process */
        $this->assertEquals('foo-bar-baz.txt', $process->getFileName());
        $this->assertEquals(305419896, $process->getTime());
        $this->assertEquals(1024, $process->getSize());
        $this->assertEquals(Fsp::RDTYPE_FILE, $process->getType());
    }

    /**
     * @throws \RemoteRequest\RequestException
     */
    public function testAnswerError()
    {
        $mock = new FspAnswerMock();
        $read = new Fsp\Answer();
//        $read->canDump = true;
        $read->setResponse($mock->getResponseError())->process();
        $process = Fsp\Answer\AnswerFactory::getObject($read)->process();
        /** @var Fsp\Answer\Error $process */
        $this->assertEquals('Testing error 1', $process->getError()->getMessage());
        $this->assertEmpty($process->getError()->getCode());
    }

    /**
     * @throws \RemoteRequest\RequestException
     */
    public function testAnswerErrorDetails()
    {
        $mock = new FspAnswerMock();
        $read = new Fsp\Answer();
        $read->setResponse($mock->getResponseErrorDetails())->process();
        $process = Fsp\Answer\AnswerFactory::getObject($read)->process();
        /** @var Fsp\Answer\Error $process */
        $this->assertEquals('Testing error 2', $process->getError()->getMessage());
        $this->assertEquals(63, $process->getError()->getCode());
    }

    /**
     * @expectedException \RemoteRequest\RequestException
     */
    public function testAnswerErrorRun()
    {
        $mock = new FspAnswerMock();
        $read = new Fsp\Answer();
        $read->setResponse($mock->getResponseError())->process();
        $process = Fsp\Answer\AnswerFactory::getObject($read)->process();
        /** @var Fsp\Answer\Error $process */
        $process->setHardWay(true)->getError();
    }

    /**
     * @throws \RemoteRequest\RequestException
     */
    public function testAnswerTest()
    {
        $mock = new FspAnswerMock();
        $read = new Fsp\Answer();
        $read->setResponse($mock->getResponseTest())->process();
        $process = Fsp\Answer\AnswerFactory::getObject($read)->process();
        /** @var Fsp\Answer\Test $process */
        $this->assertTrue(true); // nothing to do
    }

    /**
     * @throws \RemoteRequest\RequestException
     */
    public function testAnswerNothing()
    {
        $mock = new FspAnswerMock();
        $read = new Fsp\Answer();
        $read->setResponse($mock->getResponseNothing())->process();
        Fsp\Answer\AnswerFactory::getObject($read)->process();
        $this->assertTrue(true); // nothing to do
    }

    /**
     * @throws \RemoteRequest\RequestException
     */
    public function testAnswerGetFile()
    {
        $mock = new FspAnswerMock();
        $read = new Fsp\Answer();
        $read->setResponse($mock->getResponseGetFile())->process();
        $process = Fsp\Answer\AnswerFactory::getObject($read)->process();
        /** @var Fsp\Answer\GetFile $process */
        $this->assertEquals('Testing data 1', $process->getContent());
        $this->assertEquals(1024, $process->getSeek());
    }

    /**
     * @throws \RemoteRequest\RequestException
     */
    public function testAnswerUpload()
    {
        $mock = new FspAnswerMock();
        $read = new Fsp\Answer();
//        $read->canDump = true;
        $read->setResponse($mock->getResponseUpload())->process();
        $process = Fsp\Answer\AnswerFactory::getObject($read)->process();
        /** @var Fsp\Answer\Upload $process */
        $this->assertEquals(512, $process->getSeek());
    }

    /**
     * @throws \RemoteRequest\RequestException
     */
    public function testAnswerProtection()
    {
        $mock = new FspAnswerMock();
        $read = new Fsp\Answer();
        $read->setResponse($mock->getResponseProtection())->process();
        $process = Fsp\Answer\AnswerFactory::getObject($read)->process();
        /** @var Fsp\Answer\Protection $process */
        $this->assertEquals('foo/bar', $process->getDirectory());
        $this->assertFalse($process->isMy());
        $this->assertTrue($process->canList());
        $this->assertFalse($process->canReadOnlyOwner());
        $this->assertTrue($process->canCreateFile());
        $this->assertTrue($process->canRenameFile());
        $this->assertTrue($process->canDeleteFile());
        $this->assertFalse($process->canCreateDir());
        $this->assertTrue($process->containsReadme());
    }

    /**
     * @throws \RemoteRequest\RequestException
     */
    public function testAnswerStats()
    {
        $mock = new FspAnswerMock();
        $read = new Fsp\Answer();
        $read->setResponse($mock->getResponseStats())->process();
        $process = Fsp\Answer\AnswerFactory::getObject($read)->process();
        /** @var Fsp\Answer\Stats $process */
        $this->assertEquals(1577836801, $process->getTime()); // 2020-01-01T00:00:01+00:00
        $this->assertEquals(2311168, $process->getSize());
        $this->assertEquals(32, $process->getType());
    }
}