<?php

namespace kalanis\RemoteRequest\Interfaces;


/**
 * Interface IConnectionParam
 * @package kalanis\RemoteRequest\Interfaces
 * Available Connection params
 * So the things that will be set into the address on TCP/IP layer
 */
interface IConnectionParams extends ITarget
{
    /**
     * Which type of connection will be used
     * @return string
     */
    public function getSchema(): string;

    /**
     * How long the connection will be active
     * Null for non-stop
     * @return float|null
     */
    public function getTimeout(): ?float;

    /**
     * Type of simple socket connection
     * @return int
     */
    public function getProtocolVersion(): int;
}
