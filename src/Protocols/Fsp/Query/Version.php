<?php

namespace RemoteRequest\Protocols\Fsp\Query;

use RemoteRequest\Protocols\Fsp;

/**
 * Want FSP version
 */
class Version extends AQuery
{
    protected function getCommand(): int
    {
        return Fsp::CC_VERSION;
    }
}