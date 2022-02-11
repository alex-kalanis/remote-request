<?php

namespace kalanis\RemoteRequest\Schemas;


use kalanis\RemoteRequest\Interfaces\IRRTranslations;
use kalanis\RemoteRequest\Interfaces\ISchema;
use kalanis\RemoteRequest\RequestException;


/**
 * Class Factory
 * @package kalanis\RemoteRequest\Schemas
 * Factory for getting a correct connection schema
 * Define known schemas for access remote resource via php internal calls
 * @link https://www.php.net/manual/en/wrappers.php
 */
class Factory
{
    /**
     * @param IRRTranslations $lang
     * @param string $schema
     * @return ASchema
     * @throws RequestException
     */
    public static function getSchema(IRRTranslations $lang, string $schema): ASchema
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
