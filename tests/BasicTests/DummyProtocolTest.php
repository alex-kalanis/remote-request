<?php

use RemoteRequest\Protocols\Dummy;

class DummyProtocolTest extends CommonTestClass
{
    public function testQuerySimple()
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

    public function testAnswerSimple()
    {
        $libValue = new Dummy\Answer();
        $this->assertEmpty($libValue->getContent());
        $libValue->setResponse('ksdjfasdfgasdjfhsdkf');
        $this->assertEquals('ksdjfasdfgasdjfhsdkf', $libValue->getContent());
    }
}