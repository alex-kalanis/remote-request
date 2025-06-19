<?php

namespace tests\TraitsTests;


use tests\CommonTestClass;
use kalanis\RemoteRequest\Translations;


class LangTest extends CommonTestClass
{
    public function testPass(): void
    {
        $lib = new XLang();
        $lib->setRRLang(new XTrans());
        $this->assertNotEmpty($lib->getRRLang());
        $this->assertInstanceOf(XTrans::class, $lib->getRRLang());
        $lib->setRRLang(null);
        $this->assertInstanceOf(Translations::class, $lib->getRRLang());
    }
}
