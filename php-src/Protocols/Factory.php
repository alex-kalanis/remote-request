<?php

namespace kalanis\RemoteRequest\Protocols;


use kalanis\RemoteRequest;


/**
 * Class Factory
 * @package kalanis\RemoteRequest\Protocols
 * Factory to select correct layer for protocol
 */
class Factory
{
    /**
     * @param string $schema
     * @param RemoteRequest\Interfaces\IRRTranslations $lang
     * @throws RemoteRequest\RequestException
     * @return AProtocol
     */
    public static function getProtocol(string $schema, RemoteRequest\Interfaces\IRRTranslations $lang): AProtocol
    {
        switch ($schema) {
            case 'tcp':
            case 'file':
                return new Tcp();
            case 'udp':
                return new Udp();
            case 'fsp':
                return new Fsp();
            case 'http':
            case 'https':
                return new Http();
//            case 'http2':
//                return new Http2();
//            case 'http3':
//                return new Http3();
            case 'webdav':
                return new WebDAV();
//            case 'smb':
//                return new Samba();
//            case 'git':
//                return new Git();
            default:
                throw new RemoteRequest\RequestException($lang->rrSchemaUnknownResponse($schema));
        }
    }
}
