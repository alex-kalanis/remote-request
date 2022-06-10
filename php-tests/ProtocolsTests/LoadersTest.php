<?php

namespace ProtocolsTests;


use CommonTestClass;
use kalanis\RemoteRequest\Interfaces;
use kalanis\RemoteRequest\Protocols;
use kalanis\RemoteRequest\RequestException;
use kalanis\RemoteRequest\Translations;


class LoadersTest extends CommonTestClass
{
    /**
     * @throws RequestException
     */
    public function testInit(): void
    {
        $lang = new Translations();
        $this->assertInstanceOf('\kalanis\RemoteRequest\Protocols\Tcp', Protocols\Factory::getProtocol($lang, Interfaces\ISchema::SCHEMA_TCP));
        $this->assertInstanceOf('\kalanis\RemoteRequest\Protocols\Tcp', Protocols\Factory::getProtocol($lang, Interfaces\ISchema::SCHEMA_FILE));
        $this->assertInstanceOf('\kalanis\RemoteRequest\Protocols\Udp', Protocols\Factory::getProtocol($lang, Interfaces\ISchema::SCHEMA_UDP));
        $this->assertInstanceOf('\kalanis\RemoteRequest\Protocols\Http', Protocols\Factory::getProtocol($lang, 'http'));
        $this->assertInstanceOf('\kalanis\RemoteRequest\Protocols\Http', Protocols\Factory::getProtocol($lang, 'https'));
        $this->assertInstanceOf('\kalanis\RemoteRequest\Protocols\Fsp', Protocols\Factory::getProtocol($lang, 'fsp'));
//        $this->assertInstanceOf('\kalanis\RemoteRequest\Protocols\Http2', Protocols\Factory::getProtocol($lang, 'http2'));
//        $this->assertInstanceOf('\kalanis\RemoteRequest\Protocols\Http3', Protocols\Factory::getProtocol($lang, 'http3'));
        $this->assertInstanceOf('\kalanis\RemoteRequest\Protocols\WebDAV', Protocols\Factory::getProtocol($lang, 'webdav'));
//        $this->assertInstanceOf('\kalanis\RemoteRequest\Protocols\Samba', Protocols\Factory::getProtocol($lang, 'smb'));
//        $this->assertInstanceOf('\kalanis\RemoteRequest\Protocols\Git', Protocols\Factory::getProtocol($lang, 'git'));
    }

    /**
     * @throws RequestException
     */
    public function testFail(): void
    {
        $this->expectException(RequestException::class);
        Protocols\Factory::getProtocol(new Translations(), 'unknown');
    }

    public function testTcp(): void
    {
        $protocol = new Protocols\Tcp(new Translations());
        $this->assertInstanceOf('\kalanis\RemoteRequest\Schemas\Tcp', $protocol->getTarget());
        $this->assertInstanceOf('\kalanis\RemoteRequest\Protocols\Dummy\Query', $protocol->getQuery());
    }

    public function testUdp(): void
    {
        $protocol = new Protocols\Udp(new Translations());
        $this->assertInstanceOf('\kalanis\RemoteRequest\Schemas\Udp', $protocol->getTarget());
        $this->assertInstanceOf('\kalanis\RemoteRequest\Protocols\Dummy\Query', $protocol->getQuery());
    }

    public function testHttp(): void
    {
        $protocol = new Protocols\Http(new Translations());
        $this->assertInstanceOf('\kalanis\RemoteRequest\Schemas\Tcp', $protocol->getTarget());
        $this->assertInstanceOf('\kalanis\RemoteRequest\Protocols\Http\Query', $protocol->getQuery());
    }

    public function testHttps(): void
    {
        $protocol = new Protocols\Https(new Translations());
        $this->assertInstanceOf('\kalanis\RemoteRequest\Schemas\Ssl', $protocol->getTarget());
        $this->assertInstanceOf('\kalanis\RemoteRequest\Protocols\Http\Query', $protocol->getQuery());
    }

    public function testRestful(): void
    {
        $protocol = new Protocols\Restful(new Translations());
        $this->assertInstanceOf('\kalanis\RemoteRequest\Schemas\Tcp', $protocol->getTarget());
        $this->assertInstanceOf('\kalanis\RemoteRequest\Protocols\Restful\Query', $protocol->getQuery());
    }

    public function testWebDav(): void
    {
        $protocol = new Protocols\WebDAV(new Translations());
        $this->assertInstanceOf('\kalanis\RemoteRequest\Schemas\Tcp', $protocol->getTarget());
        $this->assertInstanceOf('\kalanis\RemoteRequest\Protocols\WebDAV\Query', $protocol->getQuery());
    }

    public function testFsp(): void
    {
        $protocol = new Protocols\Fsp(new Translations());
        $this->assertInstanceOf('\kalanis\RemoteRequest\Schemas\Udp', $protocol->getTarget());
        $this->assertInstanceOf('\kalanis\RemoteRequest\Protocols\Fsp\Query', $protocol->getQuery());
    }
}
