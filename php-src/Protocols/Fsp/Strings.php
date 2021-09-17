<?php

namespace kalanis\RemoteRequest\Protocols\Fsp;


/**
 * Class Strings
 * @package kalanis\RemoteRequest\Protocols\Fsp
 * Process strings and integers in FSP
 */
class Strings
{
    public static function mb_chr(int $number): string
    {
        $part = intval(round($number / 256));
        return
            (($part > 0) ? static::mb_chr($part) : '')
            . chr($number % 256);
    }

    public static function mb_ord(string $str): int
    {
        $len = strlen($str);
        $char = ($len > 1) ? substr($str, $len - 1) : $str;
        $next = ($len > 1) ? substr($str, 0, $len - 1) : '' ;
        return
            ( (!empty($next)) ? ( static::mb_ord($next) * 256 ) : 0 )
            + ( (isset($char) ) ? ord($char) : 0 )
            ;
    }

    public static function filler(int $input, int $length): string
    {
        return str_pad(
            substr(static::mb_chr($input), 0, $length),
            $length,
            chr(0),
            STR_PAD_LEFT);
    }

    public static function cutter(string $data, int $start, int $length): int
    {
        return static::mb_ord(substr($data, $start, $length));
    }
}
