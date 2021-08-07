<?php

namespace ProtocolsTests\Fsp;


use CommonTestClass;
use RemoteRequest\Connection;
use RemoteRequest\Protocols\Fsp;
use RemoteRequest\RequestException;


class AnswerMock extends Connection\Processor
{
    public function getResponseVersion(): string
    {
        return Common::makeDummyQuery([
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
        return Common::makeDummyQuery([
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
        return Common::makeDummyQuery([
            0x41, # CC_GET_DIR
            0x71, # checksum
            0x01, 0x02, # key
            0x03, 0x04, # sequence
            0x00, 0x55, # data_length
            0x00, 0x00, 0x00, 0x0C, # position
            # content...
            0x12, 0x34, 0x56, 0x78, # time
            0x00, 0x00, 0x04, 0x00, # size
            0x01,                   # type
            'foo-bar-baz.txt', 0x00, # filename - "foo-bar-baz.txt\0"
            0x00, 0x00, 0x00, # padding 28

            0x5e, 0xff, 0x3f, 0xe6, // time
            0x00, 0x00, 0x00, 0x1c, // size
            0x03, // link
            0x31, 0x35, 0x39, 0x33, 0x36, 0x37, 0x39, 0x34, 0x30, 0x38, 0x37, 0x38, 0x32, 0x2e, 0x6a, 0x70, 0x67, "\n", // 1593679408782.jpg
            0x31, 0x35, 0x39, 0x33, 0x36, 0x37, 0x37, 0x38, 0x34, 0x34, 0x37, 0x38, 0x39, 0x2e, 0x6a, 0x70, 0x67, 0x00, // 1593677844789.jpg
            0x00, 0x00, 0x00, // padding 48

            0x00, 0x00, 0x00, 0x00, // 0
            0x00, 0x00, 0x00, 0x00, // 0
            0x00, // type end 9 ; 85 databytes
        ]);
    }

    public function getResponseDirReal(): string
    {
        return Common::makeDummyQuery([
            0x41, # CC_GET_DIR
            0xd7, # checksum
            0x68, 0x7f, # key
            0x01, 0xd3, # sequence
            0x01, 0x01, # data length - 257
            0x00, 0x00, 0x00, 0x00, # position (seek)

            # content...
            0x5f, 0x01, 0xac, 0x71, // 1593945201
            0x00, 0x00, 0x10, 0x00, // 4096
            0x02, // dir
            0x2e, 0x2e, 0x00, // ..
            // no padding

            0x5e, 0xe7, 0x78, 0x2c, // 1592227884
            0x00, 0x05, 0x47, 0xf5, // 346101
            0x01, // file
            0x6f, 0x70, 0x65, 0x6e, 0x20, 0x6c, 0x65, 0x74, 0x74, 0x65, 0x72, 0x2e, 0x70, 0x6e, 0x67, 0x00, // open letter.pdf
            0x00, 0x00, 0x00, // padding

            0x5e, 0x44, 0x06, 0xc9, // 1581516489
            0x00, 0x01, 0x50, 0x6d, // 86125
            0x01, // file
            0x61, 0x73, 0x79, 0x6e, 0x63, 0x69, 0x6f, 0x2e, 0x70, 0x64, 0x66, 0x00, // asyncio.pdf
            0x00, 0x00, 0x00, // padding

            0x5e, 0xec, 0xec, 0x36, // 1592585270
            0x00, 0x01, 0x47, 0xf4, // 83956
            0x01, // file
            0x6d, 0x61, 0x6b, 0x6f, 0x74, 0x6f, 0x5f, 0x6b, 0x69, 0x6e, 0x6f, 0x5f, 0x62, 0x79, 0x5f, // makoto_kino_by_
            0x69, 0x73, 0x61, 0x63, 0x6b, 0x35, 0x30, 0x33, 0x5f, 0x64, 0x38, 0x74, 0x35, 0x31, 0x65, // isack503_d8t51e
            0x72, 0x2d, 0x66, 0x75, 0x6c, 0x6c, 0x76, 0x69, 0x65, 0x77, 0x2e, 0x6a, 0x70, 0x67, 0x00, // r-fullview.jpg ; 150
            0x00, 0x00, // padding

            0x5e, 0xec, 0xe8, 0xcb, // 1592584395
            0x00, 0x84, 0x9c, 0xb1, // 8690865
            0x01, // file
            0x62, 0x65, 0x66, 0x6f, 0x72, 0x65, 0x2d, 0x74, 0x68, 0x65, 0x2d, 0x77, 0x65, 0x64, 0x64, // before-the-wedd
            0x69, 0x6e, 0x67, 0x2d, 0x6d, 0x61, 0x6b, 0x6f, 0x74, 0x6f, 0x2d, 0x6b, 0x69, 0x6e, 0x6f, // ing-makoto-kino
            0x2d, 0x63, 0x68, 0x69, 0x62, 0x69, 0x63, 0x68, 0x69, 0x62, 0x69, 0x2d, 0x39, 0x70, 0x5a, // -chibichibi-9pZ
            0x57, 0x2e, 0x6a, 0x70, 0x67, 0x00, // W.jpg
            // no padding

            0x5e, 0xff, 0x3f, 0xe6, // 1593786342
            0x00, 0x04, 0x35, 0xc4, // 275908
            0x01, // file
            0x31, 0x35, 0x39, 0x33, 0x36, 0x37, 0x39, 0x34, 0x30, 0x38, 0x37, 0x38, 0x32, 0x2e, 0x6a, 0x70, 0x67, 0x00, // 1593679408782.jpg
            0x00, // padding

            0x5e, 0xff, 0x3f, 0x7e, // 1593786238
            0x00, 0x01, 0x7e, 0x3d, // 97853
            0x01, // file
            0x31, 0x35, 0x39, 0x33, 0x36, 0x37, 0x37, 0x38, 0x34, 0x34, 0x37, 0x38, 0x39, 0x2e, 0x6a, 0x70, 0x67, 0x00, // 1593677844789.jpg
            0x00, // padding

            0x5f, 0x01, 0xac, 0x71, // 1593945201
            0x00, 0x00, 0x10, 0x00, // 4096
            0x02, // dir
            0x2e, 0x00, // .
            0x00, // padding

            0x00, 0x00, 0x00, 0x00, // 0
            0x00, 0x00, 0x00, 0x00, // 0
            0x00, // type end; 257 databytes
        ]);
    }

    public function getResponseError(): string
    {
        return Common::makeDummyQuery([
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
        return Common::makeDummyQuery([
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
        return Common::makeDummyQuery([
            0x81, # CC_TEST
            0x8B, # checksum
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
        return Common::makeDummyQuery([
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
        return Common::makeDummyQuery([
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
        return Common::makeDummyQuery([
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
        return Common::makeDummyQuery([
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
        return Common::makeDummyQuery([
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

class AnswerTest extends CommonTestClass
{
    /**
     * @throws RequestException
     */
    public function testAnswerVersion(): void
    {
        $mock = new AnswerMock();
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
        $mock = new AnswerMock();
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
        $mock = new AnswerMock();
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
        $mock = new AnswerMock();
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
        $mock = new AnswerMock();
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
        $mock = new AnswerMock();
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
        $mock = new AnswerMock();
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
        $mock = new AnswerMock();
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
        $mock = new AnswerMock();
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
        $mock = new AnswerMock();
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
        $mock = new AnswerMock();
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
        $mock = new AnswerMock();
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
        $mock = new AnswerMock();
        $read = new Fsp\Answer();
        $read->setResponse($mock->getResponseStats())->process();
        $process = Fsp\Answer\AnswerFactory::getObject($read)->process();
        /** @var Fsp\Answer\Stats $process */
        $this->assertEquals(1577836801, $process->getTime()); // 2020-01-01T00:00:01+00:00
        $this->assertEquals(2311168, $process->getSize());
        $this->assertEquals(32, $process->getType());
    }
}
