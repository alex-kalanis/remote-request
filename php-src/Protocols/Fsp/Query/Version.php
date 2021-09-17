<?php

namespace kalanis\RemoteRequest\Protocols\Fsp\Query;


use kalanis\RemoteRequest\Protocols\Fsp;


/**
 * Class Version
 * @package kalanis\RemoteRequest\Protocols\Fsp\Query
 * Want FSP version
 */
class Version extends AQuery
{
    protected function getCommand(): int
    {
        return Fsp::CC_VERSION;
    }
}
