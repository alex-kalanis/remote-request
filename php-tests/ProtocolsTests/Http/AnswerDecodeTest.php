<?php

namespace tests\ProtocolsTests\Http;


use tests\CommonTestClass;
use kalanis\RemoteRequest\Protocols\Helper;
use kalanis\RemoteRequest\Protocols\Http;
use kalanis\RemoteRequest\RequestException;


class AnswerDecodeTest extends CommonTestClass
{
    /**
     * @throws RequestException
     */
    public function testStreamAbstract(): void
    {
        $lib = new AnswerDecode\XDecoder();
        $this->assertFalse($lib->canDecode(''));
        $this->assertTrue($lib->canDecode('custom'));

        $string = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $stream = Helper::getMemStorage();
        fwrite($stream, $string);
        rewind($stream);
        $this->assertEquals($string . '---!!!', stream_get_contents($lib->processDecode($stream), -1, 0));
    }

    public function testChunks(): void
    {
        $lib = new Http\Answer\DecodeStrings\Chunked();
        $this->assertFalse($lib->canDecode(''));
        $this->assertTrue($lib->canDecode('chunked'));
        $string = "4\r\nWiki\r\n5\r\npedia\r\nE\r\n in\r\n\r\nchunks.\r\n0\r\n\r\n";
        $this->assertEquals("Wikipedia in\r\n\r\nchunks.", $lib->processDecode($string));
    }

    /**
     * @requires extension zlib
     */
    public function testCompress(): void
    {
        $lib = new Http\Answer\DecodeStrings\Compressed();
        $this->assertFalse($lib->canDecode(''));
        $this->assertTrue($lib->canDecode('x-compress,deflate'));
        $string = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $this->assertEquals($string, $lib->processDecode(gzcompress($string)));
    }

    /**
     * @requires extension zlib
     */
    public function testDeflate(): void
    {
        $lib = new Http\Answer\DecodeStrings\Deflated();
        $this->assertFalse($lib->canDecode(''));
        $this->assertTrue($lib->canDecode('x-compress,deflate'));
        $string = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $this->assertEquals($string, $lib->processDecode(gzdeflate($string)));
    }

    /**
     * @requires extension zlib
     */
    public function testGZip(): void
    {
        $lib = new Http\Answer\DecodeStrings\Zipped();
        $this->assertFalse($lib->canDecode(''));
        $this->assertTrue($lib->canDecode('x-compress,gzip'));
        $string = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $this->assertEquals($string, $lib->processDecode(gzencode($string)));
    }

    /**
     * @requires extension zlib
     */
    public function testStringCompress(): void
    {
        $lib = new AnswerDecode\XStringDecoderCompress();
        $lib->addStringDecoding(new Http\Answer\DecodeStrings\Compressed());
        $lib->addStringDecoding(new Http\Answer\DecodeStrings\Zipped());
        $string = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $this->assertEquals($string, $lib->processStringDecode(gzcompress($string)));
    }

    /**
     * @requires extension zlib
     */
    public function testStringRaw(): void
    {
        $lib = new AnswerDecode\XStringDecoderDeflate();
        $lib->addStringDecoding(new Http\Answer\DecodeStrings\Compressed());
        $lib->addStringDecoding(new Http\Answer\DecodeStrings\Zipped());
        $string = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $this->assertEquals($string, $lib->processStringDecode($string));
    }

    /**
     * @throws RequestException
     * @requires extension zlib
     */
    public function testStreamRaw(): void
    {
        $lib = new AnswerDecode\XStreamDecoder();
        $string = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $stream = Helper::getMemStorage();
        fwrite($stream, $string);
        rewind($stream);
        $this->assertEquals($string, stream_get_contents($lib->processStreamDecode($stream), -1, 0));
    }

    /**
     * @throws RequestException
     * @requires extension zlib
     */
    public function testStreamThru(): void
    {
        $lib = new AnswerDecode\XStreamDecoder();
        $string = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $stream = Helper::getMemStorage();
        fwrite($stream, $string);
        rewind($stream);
        // nothing set
        $this->assertEquals($string, stream_get_contents($lib->processStreamDecode($stream), -1, 0));

        // added one
        rewind($stream);
        $lib->addStreamDecoder(new AnswerDecode\XDecoder());
        $this->assertEquals($string . '---!!!', stream_get_contents($lib->processStreamDecode($stream), -1, 0));
    }
}
