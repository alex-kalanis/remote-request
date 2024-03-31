<?php

namespace kalanis\RemoteRequest\Interfaces;


/**
 * Interface ISchema
 * @package kalanis\RemoteRequest\Interfaces
 * Available schemes
 * So the things that will be set into the address on TCP/IP layer
 */
interface ISchema
{
    public const SCHEMA_FILE = 'file';
    public const SCHEMA_PHP = 'php';
    public const SCHEMA_TCP = 'tcp';
    public const SCHEMA_UDP = 'udp';
    public const SCHEMA_SSL = 'ssl';
}
