<?php

namespace kalanis\RemoteRequest\Interfaces;


/**
 * Interface ISchema
 * @package kalanis\RemoteRequest\Interfaces
 * Available schemas
 */
interface ISchema
{
    const SCHEMA_FILE = 'file';
    const SCHEMA_PHP = 'php';
    const SCHEMA_TCP = 'tcp';
    const SCHEMA_UDP = 'udp';
    const SCHEMA_SSL = 'ssl';
}
