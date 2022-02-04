<?php

namespace ProtocolsTests\Fsp;


class Common
{
    public static function makeDummyQuery(array $values)
    {
        $content = implode('', array_map(['\ProtocolsTests\Fsp\Common', 'makeDummyChars'], $values));
        $res = fopen('php://temp', 'rw');
        fputs($res, $content);
        rewind($res);
        return $res;
    }

    public static function makeDummyChars($input): string
    {
        return (is_int($input)) ? chr($input) : (string)$input;
    }
}
