<?php

namespace RemoteRequest\Protocols\Fsp\Answer;

/**
 * Process test answer
 */
class Test extends AAnswer
{
    public function process(): parent
    {
        return $this;
    }
}