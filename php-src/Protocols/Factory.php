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
     * @param RemoteRequest\Interfaces\IRRTranslations $lang
     * @param string $schema
     * @return AProtocol
     * @throws RemoteRequest\RequestException
     */
    public static function getProtocol(RemoteRequest\Interfaces\IRRTranslations $lang, string $schema): AProtocol
    {
        switch ($schema) {
            case 'tcp':
            case 'file':
                return new Tcp($lang);
            case 'udp':
                return new Udp($lang);
            case 'fsp':
                return new Fsp($lang);
            case 'http':
            case 'https':
                return new Http($lang);
//            case 'http2':
//                return new Http2($lang);
//            case 'http3':
//                return new Http3($lang);
            case 'webdav':
                return new WebDAV($lang);
//            case 'smb':
//                return new Samba($lang);
//            case 'git':
//                return new Git($lang);
            default:
                throw new RemoteRequest\RequestException($lang->rrSchemaUnknownResponse($schema));
        }
    }
}
