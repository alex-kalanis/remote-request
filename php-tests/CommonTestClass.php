<?php

class CommonTestClass extends \PHPUnit\Framework\TestCase
{
//    public function providerBasic()
//    {
//        return Array(
//            0 => Array(new ORMTest()),
//            1 => Array(new ORMTestOld())
//        );
//    }

    public static function stringToResource(string $content)
    {
        $res = fopen('php://memory', 'rw');
        fputs($res, $content);
        rewind($res);
        return $res;
    }
}
