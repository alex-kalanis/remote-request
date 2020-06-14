<?php

namespace RemoteRequest\Protocols\Fsp;

/**
 * Process header
 */
trait THeader
{
    protected function renderRequestHeader(int $checksum): string
    {
        return sprintf('%s%s%s%s%s%s',
            $this->headerFill($this->getCommand(), 1),
            $this->headerFill($checksum, 1),
            $this->headerFill($this->getKey(), 2),
            $this->headerFill($this->getSequence(), 2),
            $this->headerFill(strlen($this->getContent()), 2),
            $this->headerFill($this->getFilePosition(), 4)
        );
    }

    protected function headerFill(int $input, int $length): string
    {
        return str_pad(
            $this->mb_chr($input),
            $length,
            chr(0),
            STR_PAD_LEFT);
    }

    protected function headerParse(string $header, int $start, int $length): int
    {
        return $this->mb_ord(substr($header, $start, $length));
    }

    abstract protected function getCommand(): int;

    abstract protected function getKey(): int;

    abstract protected function getSequence(): int;

    abstract protected function getFilePosition(): int;

    abstract protected function getContent(): string;

    protected function mb_chr(int $number): string
    {
        $part = intval(round($number / 256));
        return
            (($part > 0) ? $this->mb_chr($part) : '')
            . chr($number % 256);
    }

    protected function mb_ord(string $str): int
    {
        $len = strlen($str);
        $char = ($len > 1) ? substr($str, $len - 1) : $str;
        $next = ($len > 1) ? substr($str, 0, $len - 1) : '' ;
        return
            ( (!empty($next)) ? ( $this->mb_ord($next) * 256 ) : 0 )
            + ( (!empty($char)) ? ord($char) : 0 )
            ;
    }
}