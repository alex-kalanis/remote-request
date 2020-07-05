<?php

namespace RemoteRequest\Protocols\Fsp\Answer;

use RemoteRequest\Protocols\Fsp;

/**
 * Abstract processor - common calls across the answer
 */
abstract class AAnswer
{
    protected $answer = null;

    public function __construct(Fsp\Answer $answer)
    {
        $this->answer = $answer;
        $this->customInit();
    }

    protected function customInit(): void
    {
    }

    public function getDataClass(): Fsp\Answer
    {
        return $this->answer;
    }

    abstract public function process(): self;
}