<?php

namespace ProtocolsTests\Http;


use CommonTestClass;
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
        $lib = new XDecoder();
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
        $lib = new XStringDecoderCompress();
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
        $lib = new XStringDecoderDeflate();
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
        $lib = new XStreamDecoder();
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
        $lib = new XStreamDecoder();
        $string = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $stream = Helper::getMemStorage();
        fwrite($stream, $string);
        rewind($stream);
        // nothing set
        $this->assertEquals($string, stream_get_contents($lib->processStreamDecode($stream), -1, 0));

        // added one
        rewind($stream);
        $lib->addStreamDecoder(new XDecoder());
        $this->assertEquals($string . '---!!!', stream_get_contents($lib->processStreamDecode($stream), -1, 0));
    }
}


class XStreamDecoder
{
    use Http\Answer\DecodeStreams\TDecoding;

    public function getAllHeaders(): array
    {
        return [
            'Server' => ['PhpUnit/9.3.0'],
            'Content-Length' => ['25'],
            'Content-Type' => ['text/plain'],
            'Content-Encoding' => ['compress,deflate,gzip,custom'],
            'Transfer-Encoding' => ['chunked'],
            'Connection' => ['Closed'],
        ];
    }
}


class XStringDecoderCompress
{
    use Http\Answer\DecodeStrings\TDecoding;

    public function getAllHeaders(): array
    {
        return [
            'Server' => ['PhpUnit/9.3.0'],
            'Content-Length' => ['25'],
            'Content-Type' => ['text/plain'],
            'Content-Encoding' => ['compress'],
            'Transfer-Encoding' => ['chunked'],
            'Connection' => ['Closed'],
        ];
    }
}


class XStringDecoderDeflate
{
    use Http\Answer\DecodeStrings\TDecoding;

    public function getAllHeaders(): array
    {
        return [
            'Server' => ['PhpUnit/9.3.0'],
            'Content-Length' => ['25'],
            'Content-Type' => ['text/plain'],
            'Content-Encoding' => ['deflate'],
            'Transfer-Encoding' => ['chunked'],
            'Connection' => ['Closed'],
        ];
    }
}


class XDecoder extends Http\Answer\DecodeStreams\ADecoder
{
    protected $contentEncoded = ['custom'];

    public function processDecode($content)
    {
        fseek($content, 0, SEEK_END);
        fwrite($content, '---!!!');
        return $content;
    }
}
