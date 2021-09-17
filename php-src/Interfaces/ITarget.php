<?php

namespace kalanis\RemoteRequest\Interfaces;


/**
 * Interface ITarget
 * @package kalanis\RemoteRequest\Interfaces
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
