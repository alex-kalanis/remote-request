<?php

namespace ProtocolsTests\Http;

use CommonTestClass;
use RemoteRequest\Protocols\Http;

class ProtocolTest extends CommonTestClass
{
    public function testValueSimple()
    {
        $libValue1 = new Http\Query\Value();
        $this->assertEquals('', $libValue1->getContent());
        $libValue1->setContent('poiuz');
        $this->assertEquals('poiuz', $libValue1->getContent());
        $libValue2 = $this->prepareTestValue('lkjhg');
        $this->assertEquals('lkjhg', $libValue2->getContent());
    }

    public function testValueFile()
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

    protected function prepareTestValue($content)
    {
        return new Http\Query\Value($content);
    }

    protected function prepareTestFile($content)
    {
        $libValue = new Http\Query\File($content);
        $libValue->filename = 'dummy.txt';
        $libValue->mime = 'text/plain';
        return $libValue;
    }
}