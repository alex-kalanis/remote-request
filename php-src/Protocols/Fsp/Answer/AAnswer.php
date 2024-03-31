<?php

namespace kalanis\RemoteRequest\Protocols\Fsp\Answer;


use kalanis\RemoteRequest\Protocols\Fsp;


/**
 * Class AAnswer
 * @package kalanis\RemoteRequest\Protocols\Fsp\Answer
 * Abstract processor - common calls across the answer
 */
abstract class AAnswer
{
    protected Fsp\Answer $answer;

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
