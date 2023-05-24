<?php

namespace kalanis\RemoteRequest\Connection\Params;


use kalanis\RemoteRequest\Interfaces\IRRTranslations;
use kalanis\RemoteRequest\Interfaces\ISchema;
use kalanis\RemoteRequest\RequestException;


/**
 * Class Factory
 * @package kalanis\RemoteRequest\Connection\Params
 * Factory for getting a correct connection scheme
 * Define known schemes for access remote resource via php internal calls
 * @link https://www.php.net/manual/en/wrappers.php
 */
class Factory
{
    /**
     * @param string $schema
     * @param IRRTranslations $lang
     * @throws RequestException
     * @return AParams
     */
    public static function getForSchema(string $schema, IRRTranslations $lang): AParams
    {
        switch ($schema) {
            case ISchema::SCHEMA_FILE:
                return new File();
            case ISchema::SCHEMA_PHP:
                return new Php();
            case ISchema::SCHEMA_TCP:
                return new Tcp();
            case ISchema::SCHEMA_UDP:
                return new Udp();
            case ISchema::SCHEMA_SSL:
                return new Ssl();
            default:
                throw new RequestException($lang->rrSchemaUnknownPacketWrapper());
        }
    }
}
