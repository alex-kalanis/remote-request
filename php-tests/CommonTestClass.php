<?php

namespace tests;

class CommonTestClass extends \PHPUnit\Framework\TestCase
{
    /**
     * @param string $content
     * @throws \kalanis\RemoteRequest\RequestException
     * @return resource
     */
    public static function stringToResource(string $content)
    {
        $res = \kalanis\RemoteRequest\Protocols\Helper::getMemStorage();
        fwrite($res, $content);
        rewind($res);
        return $res;
    }
}
