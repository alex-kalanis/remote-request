<?php

namespace kalanis\RemoteRequest\Connection;


/**
 * Interface ITarget
 * @package kalanis\RemoteRequest\Connection
 * Settings of remote connection
 */
interface ITarget
{
    /**
     * Remote server (ip or domain)
     * @return string
     */
    public function getHost(): string;

    /**
     * Remote server port
     * @return int|null
     */
    public function getPort(): ?int;
}
