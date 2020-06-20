<?php

namespace RemoteRequest\Protocols\Fsp\Query;

use RemoteRequest\Protocols\Fsp;

/**
 * End from this machine, leave line for others
 */
class Bye extends AQuery
{
    protected function getCommand(): int
    {
        return Fsp::CC_BYE;
    }
}