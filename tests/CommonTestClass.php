<?php

function fspMakeDummyQuery(array $values) {
    return implode('', array_map('chr', $values));
}

class CommonTestClass extends \PHPUnit\Framework\TestCase
{
//    public function providerBasic()
//    {
//        return Array(
//            0 => Array(new ORMTest()),
//            1 => Array(new ORMTestOld())
//        );
//    }
}