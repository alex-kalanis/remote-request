<?php

namespace RemoteRequest\Connection;

/**
 * Settings of remote connection
 */
interface ISettings
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