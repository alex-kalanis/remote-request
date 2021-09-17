<?php

namespace kalanis\RemoteRequest\Protocols\Fsp\Query;


use kalanis\RemoteRequest\Protocols\Fsp;


/**
 * Class Bye
 * @package kalanis\RemoteRequest\Protocols\Fsp\Query
 * End from this machine, leave line for others
 */
class Bye extends AQuery
{
    protected function getCommand(): int
    {
        return Fsp::CC_BYE;
    }
}
