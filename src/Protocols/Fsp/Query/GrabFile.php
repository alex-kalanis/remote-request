<?php

namespace RemoteRequest\Protocols\Fsp\Query;


use RemoteRequest\Protocols\Fsp;


/**
 * Class GrabFile
 * @package RemoteRequest\Protocols\Fsp\Query
 * Want file part - and then delete it!
 */
class GrabFile extends GetFile
{
    protected function getCommand(): int
    {
        return Fsp::CC_GRAB_FILE;
    }
}
