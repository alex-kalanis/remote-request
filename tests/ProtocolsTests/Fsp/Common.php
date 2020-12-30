<?php

namespace ProtocolsTests\Fsp;


class Common
{
    public static function makeDummyQuery(array $values): string
    {
        return implode('', array_map(['\ProtocolsTests\Fsp\Common', 'makeDummyChars'], $values));
    }

    public static function makeDummyChars($input): string
    {
        return (is_int($input)) ? chr($input) : (string)$input;
    }
}
