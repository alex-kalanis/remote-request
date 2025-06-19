<?php

namespace tests\ProtocolsTests\Fsp;


use kalanis\RemoteRequest\Protocols\Fsp;
use kalanis\RemoteRequest\RequestException;
use tests\CommonTestClass;


class AnswerTest extends CommonTestClass
{
    /**
     * @throws RequestException
     */
    public function testAnswerVersion(): void
    {
        $mock = new Answer\AnswerMock();
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
     * @throws RequestException
     */
    public function testAnswerVersionPayload(): void
    {
        $mock = new Answer\AnswerMock();
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
     * @throws RequestException
     */
    public function testAnswerDir(): void
    {
        $mock = new Answer\AnswerMock();
        $read = new Fsp\Answer();
//        $read->canDump = true;
        $read->setResponse($mock->getResponseDir())->process();
        $process = Fsp\Answer\AnswerFactory::getObject($read)->process();
        /** @var Fsp\Answer\GetDir $process */
        $files = $process->getFiles();
        $file = reset($files);
        /** @var Fsp\Answer\GetDir\FileInfo $file */
        $this->assertEquals('foo-bar-baz.txt', $file->getFileName());
        $this->assertEquals('', $file->getLinkTarget());
        $this->assertEquals('txt', $file->getExtension());
        $this->assertEquals(305419896, $file->getCTime());
        $this->assertEquals(305419896, $file->getMTime());
        $this->assertEquals(305419896, $file->getATime());
        $this->assertEquals(1024, $file->getSize());
        $this->assertEquals('file', $file->getType());
        $this->assertEquals(Fsp::RDTYPE_FILE, $file->getOrigType());
        $this->assertTrue($file->isReadable());
        $this->assertTrue($file->isWritable());
        $this->assertTrue($file->isFile());
        $this->assertFalse($file->isDir());
        $this->assertFalse($file->isLink());
        $this->assertFalse($file->isExecutable());
        $this->assertEquals(0, $file->getOwner());
        $this->assertEquals(0, $file->getGroup());
        $this->assertEquals(0, $file->getInode());
        $this->assertEquals(0666, $file->getPerms());
        $file = next($files);
        $file->setPath('foo/baz');
        $this->assertEquals('foo/baz', $file->getPath());
        $this->assertEquals('1593679408782.jpg', $file->getFileName());
        $this->assertEquals('1593677844789.jpg', $file->getLinkTarget());
        $this->assertEquals('foo/baz/1593679408782.jpg', $file->getPathname());
        $this->assertEquals('foo/baz/1593679408782.jpg', $file->getRealPath());
    }

    /**
     * @throws RequestException
     */
    public function testAnswerDirReal(): void
    {
        $mock = new Answer\AnswerMock();
        $read = new Fsp\Answer();
        $read->setResponse($mock->getResponseDirReal())->process();
        $process = Fsp\Answer\AnswerFactory::getObject($read)->process();
        /** @var Fsp\Answer\GetDir $process */
        $files = $process->getFiles();
        reset($files);
        $file = next($files);
        /** @var Fsp\Answer\GetDir\FileInfo $file */
        $this->assertEquals('open letter.png', $file->getFileName());
        $this->assertEquals(1592227884, $file->getMTime());
        $this->assertEquals(346101, $file->getSize());
        $this->assertFalse($file->isDir());
        $this->assertTrue($file->isFile());
        $file = next($files);
        $this->assertEquals('asyncio.pdf', $file->getFileName());
        $this->assertEquals(1581516489, $file->getMTime());
        $this->assertEquals(86125, $file->getSize());
        $this->assertFalse($file->isDir());
        $this->assertTrue($file->isFile());
        $file = next($files);
        $this->assertEquals('makoto_kino_by_isack503_d8t51er-fullview.jpg', $file->getFileName());
        $this->assertEquals(1592585270, $file->getMTime());
        $this->assertEquals(83956, $file->getSize());
        $this->assertFalse($file->isDir());
        $this->assertTrue($file->isFile());
    }

    /**
     * @throws RequestException
     */
    public function testAnswerError(): void
    {
        $mock = new Answer\AnswerMock();
        $read = new Fsp\Answer();
//        $read->canDump = true;
        $read->setResponse($mock->getResponseError())->process();
        $process = Fsp\Answer\AnswerFactory::getObject($read)->process();
        /** @var Fsp\Answer\Error $process */
        $this->assertEquals('Testing error 1', $process->getError()->getMessage());
        $this->assertEmpty($process->getError()->getCode());
    }

    /**
     * @throws RequestException
     */
    public function testAnswerErrorDetails(): void
    {
        $mock = new Answer\AnswerMock();
        $read = new Fsp\Answer();
        $read->setResponse($mock->getResponseErrorDetails())->process();
        $process = Fsp\Answer\AnswerFactory::getObject($read)->process();
        /** @var Fsp\Answer\Error $process */
        $this->assertEquals('Testing error 2', $process->getError()->getMessage());
        $this->assertEquals(63, $process->getError()->getCode());
    }

    /**
     * @throws RequestException
     */
    public function testAnswerErrorRun(): void
    {
        $mock = new Answer\AnswerMock();
        $read = new Fsp\Answer();
        $read->setResponse($mock->getResponseError())->process();
        $process = Fsp\Answer\AnswerFactory::getObject($read)->process();
        /** @var Fsp\Answer\Error $process */
        $this->expectException(RequestException::class);
        $process->setHardWay(true)->getError();
    }

    /**
     * @throws RequestException
     */
    public function testAnswerTest(): void
    {
        $mock = new Answer\AnswerMock();
        $read = new Fsp\Answer();
        $read->setResponse($mock->getResponseTest())->process();
        $process = Fsp\Answer\AnswerFactory::getObject($read)->process();
        /** @var Fsp\Answer\Test $process */
        $this->assertTrue(true); // nothing to do
    }

    /**
     * @throws RequestException
     */
    public function testAnswerNothing(): void
    {
        $mock = new Answer\AnswerMock();
        $read = new Fsp\Answer();
        $read->setResponse($mock->getResponseNothing())->process();
        Fsp\Answer\AnswerFactory::getObject($read)->process();
        $this->assertTrue(true); // nothing to do
    }

    /**
     * @throws RequestException
     */
    public function testAnswerGetFile(): void
    {
        $mock = new Answer\AnswerMock();
        $read = new Fsp\Answer();
        $read->setResponse($mock->getResponseGetFile())->process();
        $process = Fsp\Answer\AnswerFactory::getObject($read)->process();
        /** @var Fsp\Answer\GetFile $process */
        $this->assertEquals('Testing data 1', $process->getContent());
        $this->assertEquals(1024, $process->getSeek());
    }

    /**
     * @throws RequestException
     */
    public function testAnswerUpload(): void
    {
        $mock = new Answer\AnswerMock();
        $read = new Fsp\Answer();
//        $read->canDump = true;
        $read->setResponse($mock->getResponseUpload())->process();
        $process = Fsp\Answer\AnswerFactory::getObject($read)->process();
        /** @var Fsp\Answer\Upload $process */
        $this->assertEquals(512, $process->getSeek());
    }

    /**
     * @throws RequestException
     */
    public function testAnswerProtection(): void
    {
        $mock = new Answer\AnswerMock();
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
     * @throws RequestException
     */
    public function testAnswerStats(): void
    {
        $mock = new Answer\AnswerMock();
        $read = new Fsp\Answer();
        $read->setResponse($mock->getResponseStats())->process();
        $process = Fsp\Answer\AnswerFactory::getObject($read)->process();
        /** @var Fsp\Answer\Stats $process */
        $this->assertEquals(1577836801, $process->getTime()); // 2020-01-01T00:00:01+00:00
        $this->assertEquals(2311168, $process->getSize());
        $this->assertEquals(32, $process->getType());
    }
}
