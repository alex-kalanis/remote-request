<?php

namespace ProtocolsTests;


use CommonTestClass;
use kalanis\RemoteRequest\Protocols\Dummy;
use kalanis\RemoteRequest\RequestException;


class DummyTest extends CommonTestClass
{
    public function testQuerySimple(): void
    {
        $libValue = new Dummy\Query();
        $this->assertNull($libValue->maxLength);
        $this->assertEquals('', $libValue->body);
        $libValue->body = 'qwertzuiop';
        $libValue->setExpectedAnswerSize(55);
        $this->assertEquals(55, $libValue->getMaxAnswerLength());
        $libValue->maxLength = null;
        $this->assertNull($libValue->getMaxAnswerLength());
    }

    /**
     * @throws RequestException
     */
    public function testAnswerSimple(): void
    {
        $libValue = new Dummy\Answer();
        $this->assertEmpty($libValue->getContent());
        $libValue->setResponse(static::stringToResource('ksdjfasdfgasdjfhsdkf'));
        $this->assertEquals('ksdjfasdfgasdjfhsdkf', $libValue->getContent());
    }
}
