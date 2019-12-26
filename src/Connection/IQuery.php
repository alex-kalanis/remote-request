<?php

namespace RemoteRequest\Connection;

/**
 * Content params for connection
 */
interface IQuery
{
    /**
     * Query itself
     * @return string
     */
    public function getData(): string;

    /**
     * How many bytes it expects in the answer?
     * Null for everything
     * 0 does not wait for response
     * Number for length
     * @return int|null
     */
    public function getMaxAnswerLength(): ?int;
}