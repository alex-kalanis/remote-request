<?php

namespace kalanis\RemoteRequest\Protocols\Fsp\Query;


use kalanis\RemoteRequest\Protocols\Fsp;


/**
 * Class GrabDone
 * @package kalanis\RemoteRequest\Protocols\Fsp\Query
 * Grab file - done atomic downloading
 */
class GrabDone extends Install
{
    protected function getCommand(): int
    {
        return Fsp::CC_GRAB_DONE;
    }
}
