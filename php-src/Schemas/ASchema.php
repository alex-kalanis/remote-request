<?php

namespace kalanis\RemoteRequest\Schemas;


use kalanis\RemoteRequest\Interfaces;


/**
 * Class ASchema
 * @package kalanis\RemoteRequest\Schemas
 * Schemas for creating a connection
 * Define known schemas for access remote resource via php internal calls
 * @link https://www.php.net/manual/en/wrappers.php
 */
abstract class ASchema implements Interfaces\ITarget
{
    /** @var string */
    protected $host = '';
    /** @var int */
    protected $port = 1;
    /** @var int */
    protected $timeout = 30;

    abstract protected function getSchemaType(): string;

    public function setTarget(string $host = null, int $port = null, int $timeout = null)
    {
        $this->host = !is_null($host) ? $host : $this->host;
        $this->port = !is_null($port) ? $port : $this->port;
        $this->timeout = !is_null($timeout) ? $timeout : $this->timeout;
        return $this;
    }

    public function setRequest(Interfaces\ITarget $request): self
    {
        $this->host = $request->getHost();
        $this->port = $request->getPort();
        return $this;
    }

    public function getHostname(): string
    {
        return $this->getSchemaProtocol() . $this->getHost();
    }

    /**
     * Generate correct hostname
     * This method updates IPv6 address into form that is usable by sockets
     */
    public function getHost(): string
    {
        $host = '' . $this->host;
        return (preg_match('#^[0-9a-f:]+$#', $host) ? '[' . $host .']' : $host ); // IPv6
    }

    public function getPort(): ?int
    {
        return intval($this->port);
    }

    public function getTimeout(): ?int
    {
        return intval($this->timeout);
    }

    public function getProtocol(): int
    {
        return 0;
    }

    /**
     * Get one of available IP network packet wrappers
     * default behavior falls into TCP by PHP
     * @return string
     */
    protected function getSchemaProtocol(): string
    {
        return in_array($this->getSchemaType(), [
                Interfaces\ISchema::SCHEMA_FILE,
                Interfaces\ISchema::SCHEMA_PHP,
                Interfaces\ISchema::SCHEMA_TCP,
                Interfaces\ISchema::SCHEMA_UDP,
                Interfaces\ISchema::SCHEMA_SSL,
            ])
            ? ($this->getSchemaType() . '://')
            : ''
        ;
    }
}
