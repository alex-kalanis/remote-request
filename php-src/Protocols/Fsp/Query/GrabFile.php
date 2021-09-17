<?php

namespace kalanis\RemoteRequest\Protocols\Fsp\Query;


use kalanis\RemoteRequest\Protocols\Fsp;


/**
 * Class GrabFile
 * @package kalanis\RemoteRequest\Protocols\Fsp\Query
 * Want file part - and then delete it!
 */
class GrabFile extends GetFile
{
    protected function getCommand(): int
    {
        return Fsp::CC_GRAB_FILE;
    }
}
