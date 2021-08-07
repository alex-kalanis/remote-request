<?php

namespace RemoteRequest\Protocols\Fsp\Query;


use RemoteRequest\Protocols\Fsp;


/**
 * Class Bye
 * @package RemoteRequest\Protocols\Fsp\Query
 * End from this machine, leave line for others
 */
class Bye extends AQuery
{
    protected function getCommand(): int
    {
        return Fsp::CC_BYE;
    }
}
