<?php

namespace RemoteRequest\Wrappers;

use RemoteRequest;

/**
 * Wrappers for creating a connection
 * Define known wrapper with schema for access remote resource via php internal calls
 * @link https://www.php.net/manual/en/wrappers.php
 */
abstract class AWrapper implements RemoteRequest\Connection\ISettings
{
    const SCHEMA_FILE = 'file';
    const SCHEMA_PHP = 'php';
    const SCHEMA_TCP = 'tcp';
    const SCHEMA_TCP6 = 'tcp6'; // prepared
    const SCHEMA_UDP = 'udp';
    const SCHEMA_UDP6 = 'udp6'; // prepared
    const SCHEMA_SSL = 'ssl';

    /** @var string */
    protected $host = '';
    /** @var int */
    protected $port = 1;
    /** @var int */
    protected $timeout = 30;

    abstract public function getSchemaType(): string;

    public function setTarget(string $host = null, int $port = null, int $timeout = null)
    {
        $this->host = !is_null($host) ? $host : $this->host;
        $this->port = !is_null($port) ? $port : $this->port;
        $this->timeout = !is_null($timeout) ? $timeout : $this->timeout;
        return $this;
    }

    public function setRequest(RemoteRequest\Connection\ISettings $request)
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

    /**
     * Get one of available IP network packet wrappers
     * default behavior falls into TCP by PHP
     * @return string
     */
    public function getSchemaProtocol(): string
    {
        return in_array($this->getSchemaType(), [
                static::SCHEMA_FILE,
                static::SCHEMA_PHP,
                static::SCHEMA_TCP,
                static::SCHEMA_UDP,
                static::SCHEMA_SSL,
            ])
            ? ($this->getSchemaType() . '://')
            : ''
        ;
    }

    public static function getWrapper(int $type): AWrapper
    {
        switch ($type) {
            case static::SCHEMA_FILE:
                return new File();
            case static::SCHEMA_PHP:
                return new Php();
            case static::SCHEMA_TCP:
                return new Tcp();
            case static::SCHEMA_UDP:
                return new Udp();
            case static::SCHEMA_SSL:
                return new Ssl();
            default:
                throw new RemoteRequest\RequestException('Unknown packet wrapper type');
        }
    }
}
