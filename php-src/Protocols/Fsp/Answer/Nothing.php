<?php

namespace kalanis\RemoteRequest\Protocols\Fsp\Answer;


/**
 * Class Nothing
 * @package kalanis\RemoteRequest\Protocols\Fsp\Answer
 * Process empty answer
 */
class Nothing extends AAnswer
{
    public function process(): parent
    {
        return $this;
    }
}
