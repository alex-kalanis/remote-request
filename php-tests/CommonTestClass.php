<?php

class CommonTestClass extends \PHPUnit\Framework\TestCase
{
    public static function stringToResource(string $content)
    {
        $res = \kalanis\RemoteRequest\Protocols\Helper::getMemStorage();
        fwrite($res, $content);
        rewind($res);
        return $res;
    }
}
