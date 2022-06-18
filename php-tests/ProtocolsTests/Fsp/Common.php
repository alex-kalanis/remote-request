<?php

namespace ProtocolsTests\Fsp;


class Common
{
    public static function makeDummyQuery(array $values)
    {
        $res = fopen('php://temp', 'rw');
        fputs($res, static::makeDummyString($values));
        rewind($res);
        return $res;
    }

    public static function makeDummyString(array $values)
    {
        return implode('', array_map(['\ProtocolsTests\Fsp\Common', 'makeDummyChars'], $values));
    }

    public static function makeDummyChars($input): string
    {
        return (is_int($input)) ? chr($input) : strval($input);
    }
}
