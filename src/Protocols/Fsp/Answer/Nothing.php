<?php

namespace RemoteRequest\Protocols\Fsp\Answer;

/**
 * Process empty answer
 */
class Nothing extends AAnswer
{
    public function process(): parent
    {
        return $this;
    }
}