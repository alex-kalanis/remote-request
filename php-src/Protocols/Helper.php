<?php

namespace kalanis\RemoteRequest\Protocols;


use kalanis\RemoteRequest\RequestException;


/**
 * Class Helper
 * @package kalanis\RemoteRequest\Protocols
 */
class Helper
{
    /**
     * @throws RequestException
     * @return resource
     */
    public static function getTempStorage()
    {
        return static::getStorageResource('php://temp');
    }

    /**
     * @throws RequestException
     * @return resource
     */
    public static function getMemStorage()
    {
        return static::getStorageResource('php://memory');
    }

    /**
     * @param string $path
     * @throws RequestException
     * @return resource
     */
    protected static function getStorageResource(string $path)
    {
        $res = fopen($path, 'rw');
        if (false === $res) {
            // @codeCoverageIgnoreStart
            throw new RequestException('Cannot open temporary storage!');
        }
        // @codeCoverageIgnoreEnd
        rewind($res);
        return $res;
    }
}
