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
    const SCHEMA_FILE = 'file';
    const SCHEMA_PHP = 'php';
    const SCHEMA_TCP = 'tcp';
    const SCHEMA_UDP = 'udp';
    const SCHEMA_SSL = 'ssl';
}
