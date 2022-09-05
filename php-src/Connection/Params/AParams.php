<?php

namespace kalanis\RemoteRequest\Connection\Params;


use kalanis\RemoteRequest\Interfaces;


/**
 * Class AParams
 * @package kalanis\RemoteRequest\Connection\Params
 * Schemes for creating a connection
 * Define known schemes for access remote resource via php internal calls
 * @link https://www.php.net/manual/en/wrappers.php
 */
abstract class AParams implements Interfaces\IConnectionParams
{
    /** @var string */
    protected $host = '';
    /** @var int */
    protected $port = 1;
    /** @var int|float */
    protected $timeout = 30.0;

    abstract protected function getSchemaType(): string;

    public function setTarget(string $host = null, int $port = null, int $timeout = null): self
    {
        $this->host = $host ?? $this->host;
        $this->port = $port ?? $this->port;
        $this->timeout = $timeout ?? $this->timeout;
        return $this;
    }

    public function setRequest(Interfaces\ITarget $request): self
    {
        $this->host = $request->getHost();
        $this->port = $request->getPort() ?? $this->port;
        return $this;
    }

    /**
     * Generate correct hostname
     * This method updates IPv6 address into form that is usable by sockets
     */
    public function getHost(): string
    {
        $host = '' . $this->host;
        return (preg_match('#^[0-9a-f:]+$#', $host) ? '[' . $host . ']' : $host ); // IPv6
    }

    public function getPort(): ?int
    {
        return empty($this->port) ? null : intval($this->port);
    }

    public function getTimeout(): ?float
    {
        return empty($this->timeout) ? null : floatval($this->timeout);
    }

    public function getProtocolVersion(): int
    {
        return 0;
    }

    /**
     * Get one of available IP network packet wrappers
     * default behavior falls into TCP by PHP
     * @return string
     */
    public function getSchema(): string
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
