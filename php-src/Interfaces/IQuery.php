<?php

namespace kalanis\RemoteRequest\Interfaces;


/**
 * Interface IQuery
 * @package kalanis\RemoteRequest\Interfaces
 * Content params for connection
 */
interface IQuery
{
    /**
     * Query itself
     * @return resource
     */
    public function getData();

    /**
     * How many bytes it expects in the answer?
     * Null for everything
     * 0 does not wait for response
     * Number for length
     * @return int|null
     */
    public function getMaxAnswerLength(): ?int;
}
