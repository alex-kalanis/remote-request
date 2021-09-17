<?php

namespace ProtocolsTests;


use CommonTestClass;
use kalanis\RemoteRequest\Interfaces;
use kalanis\RemoteRequest\Protocols;
use kalanis\RemoteRequest\RequestException;


class LoadersTest extends CommonTestClass
{
    /**
     * @throws RequestException
     */
    public function testInit(): void
    {
        $this->assertInstanceOf('\kalanis\RemoteRequest\Protocols\Tcp', Protocols\Factory::getProtocol(Interfaces\ISchema::SCHEMA_TCP));
        $this->assertInstanceOf('\kalanis\RemoteRequest\Protocols\Tcp', Protocols\Factory::getProtocol(Interfaces\ISchema::SCHEMA_FILE));
        $this->assertInstanceOf('\kalanis\RemoteRequest\Protocols\Udp', Protocols\Factory::getProtocol(Interfaces\ISchema::SCHEMA_UDP));
        $this->assertInstanceOf('\kalanis\RemoteRequest\Protocols\Http', Protocols\Factory::getProtocol('http'));
        $this->assertInstanceOf('\kalanis\RemoteRequest\Protocols\Http', Protocols\Factory::getProtocol('https'));
        $this->assertInstanceOf('\kalanis\RemoteRequest\Protocols\Fsp', Protocols\Factory::getProtocol('fsp'));
//        $this->assertInstanceOf('\kalanis\RemoteRequest\Protocols\Http2', Protocols\Factory::getProtocol('http2'));
//        $this->assertInstanceOf('\kalanis\RemoteRequest\Protocols\Http3', Protocols\Factory::getProtocol('http3'));
//        $this->assertInstanceOf('\kalanis\RemoteRequest\Protocols\WebDAV', Protocols\Factory::getProtocol('webdav'));
//        $this->assertInstanceOf('\kalanis\RemoteRequest\Protocols\Samba', Protocols\Factory::getProtocol('smb'));
//        $this->assertInstanceOf('\kalanis\RemoteRequest\Protocols\Git', Protocols\Factory::getProtocol('git'));
    }

    /**
     * @throws RequestException
     */
    public function testFail(): void
    {
        $this->expectException(RequestException::class);
        Protocols\Factory::getProtocol('unknown');
    }

    public function testTcp(): void
    {
        $protocol = new Protocols\Tcp();
        $this->assertInstanceOf('\kalanis\RemoteRequest\Schemas\Tcp', $protocol->getTarget());
        $this->assertInstanceOf('\kalanis\RemoteRequest\Protocols\Dummy\Query', $protocol->getQuery());
    }

    public function testUdp(): void
    {
        $protocol = new Protocols\Udp();
        $this->assertInstanceOf('\kalanis\RemoteRequest\Schemas\Udp', $protocol->getTarget());
        $this->assertInstanceOf('\kalanis\RemoteRequest\Protocols\Dummy\Query', $protocol->getQuery());
    }

    public function testHttp(): void
    {
        $protocol = new Protocols\Http();
        $this->assertInstanceOf('\kalanis\RemoteRequest\Schemas\Tcp', $protocol->getTarget());
        $this->assertInstanceOf('\kalanis\RemoteRequest\Protocols\Http\Query', $protocol->getQuery());
    }

    public function testHttps(): void
    {
        $protocol = new Protocols\Https();
        $this->assertInstanceOf('\kalanis\RemoteRequest\Schemas\Ssl', $protocol->getTarget());
        $this->assertInstanceOf('\kalanis\RemoteRequest\Protocols\Http\Query', $protocol->getQuery());
    }

    public function testRestful(): void
    {
        $protocol = new Protocols\Restful();
        $this->assertInstanceOf('\kalanis\RemoteRequest\Schemas\Tcp', $protocol->getTarget());
        $this->assertInstanceOf('\kalanis\RemoteRequest\Protocols\Restful\Query', $protocol->getQuery());
    }

    public function testFsp(): void
    {
        $protocol = new Protocols\Fsp();
        $this->assertInstanceOf('\kalanis\RemoteRequest\Schemas\Udp', $protocol->getTarget());
        $this->assertInstanceOf('\kalanis\RemoteRequest\Protocols\Fsp\Query', $protocol->getQuery());
    }
}
