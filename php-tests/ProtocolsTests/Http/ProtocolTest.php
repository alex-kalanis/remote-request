<?php

namespace tests\ProtocolsTests\Http;


use tests\CommonTestClass;
use kalanis\RemoteRequest\Protocols\Helper;
use kalanis\RemoteRequest\Protocols\Http;
use kalanis\RemoteRequest\RequestException;


class ProtocolTest extends CommonTestClass
{
    public function testValueSimple(): void
    {
        $libValue1 = new Http\Query\Value();
        $this->assertEquals('', $libValue1->getContent());
        $libValue1->setContent('poiuz');
        $this->assertEquals('poiuz', $libValue1->getContent());
        $libValue2 = $this->prepareTestValue('lkjhg');
        $this->assertEquals('lkjhg', $libValue2->getContent());
    }

    public function testValueFile(): void
    {
        $libValue1 = new Http\Query\File();
        $this->assertEquals('', $libValue1->getContent());
        $this->assertEquals('binary', $libValue1->getFilename());
        $this->assertEquals('octet/stream', $libValue1->getMimeType());
        $libValue2 = $this->prepareTestFile('lkjhgfdsa');
        $this->assertEquals('lkjhgfdsa', $libValue2->getContent());
        $this->assertEquals('dummy.txt', $libValue2->getFilename());
        $this->assertEquals('text/plain', $libValue2->getMimeType());
    }

    /**
     * @throws RequestException
     */
    public function testValueFileStream1(): void
    {
        $libValue = $this->prepareTestFile('lkjhgfdsa');
        $this->assertEquals('lkjhgfdsa', $libValue->getContent());
        $this->assertEquals('lkjhgfdsa', stream_get_contents($libValue->getStream(), -1, 0));
        $cnt = Helper::getMemStorage();
        fwrite($cnt, 'okmijnuhbzgvtfcdrxsey');
        $libValue->setContent($cnt);
        $this->assertEquals('okmijnuhbzgvtfcdrxsey', $libValue->getContent());
        $this->assertEquals('okmijnuhbzgvtfcdrxsey', stream_get_contents($libValue->getStream(), -1, 0));
    }

    protected function prepareTestValue($content): Http\Query\Value
    {
        return new Http\Query\Value($content);
    }

    protected function prepareTestFile($content): Http\Query\File
    {
        $libValue = new Http\Query\File($content);
        $libValue->filename = 'dummy.txt';
        $libValue->mime = 'text/plain';
        return $libValue;
    }
}
