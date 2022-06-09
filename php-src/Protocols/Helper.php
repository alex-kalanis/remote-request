<?php

namespace kalanis\RemoteRequest\Protocols;


/**
 * Class Helper
 * @package kalanis\RemoteRequest\Protocols
 */
class Helper
{
    /**
     * @return null|resource
     */
    public static function getTempStorage()
    {
        return static::getStorageResource('php://temp');
    }

    /**
     * @return null|resource
     */
    public static function getMemStorage()
    {
        return static::getStorageResource('php://memory');
    }

    protected static function getStorageResource(string $path)
    {
        $res = fopen($path, 'rw');
        if (false === $res) {
            // @codeCoverageIgnoreStart
            return null;
        }
        // @codeCoverageIgnoreEnd
        rewind($res);
        return $res;
    }
}
