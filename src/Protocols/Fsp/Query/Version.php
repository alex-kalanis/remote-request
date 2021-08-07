<?php

namespace RemoteRequest\Protocols\Fsp\Query;


use RemoteRequest\Protocols\Fsp;


/**
 * Class Version
 * @package RemoteRequest\Protocols\Fsp\Query
 * Want FSP version
 */
class Version extends AQuery
{
    protected function getCommand(): int
    {
        return Fsp::CC_VERSION;
    }
}
