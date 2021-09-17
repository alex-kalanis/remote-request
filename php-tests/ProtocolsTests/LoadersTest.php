<?php

namespace ProtocolsTests;


use CommonTestClass;
use kalanis\RemoteRequest\Protocols;
use kalanis\RemoteRequest\RequestException;
use kalanis\RemoteRequest\Schemas;


class LoadersTest extends CommonTestClass
{
    /**
     * @throws RequestException
     */
    public function testInit(): void
    {
        $this->assertInstanceOf('\kalanis\RemoteRequest\Protocols\Tcp', Protocols\AProtocol::getProtocol(Schemas\ASchema::SCHEMA_TCP));
        $this->assertInstanceOf('\kalanis\RemoteRequest\Protocols\Tcp', Protocols\AProtocol::getProtocol(Schemas\ASchema::SCHEMA_FILE));
        $this->assertInstanceOf('\kalanis\RemoteRequest\Protocols\Udp', Protocols\AProtocol::getProtocol(Schemas\ASchema::SCHEMA_UDP));
        $this->assertInstanceOf('\kalanis\RemoteRequest\Protocols\Http', Protocols\AProtocol::getProtocol('http'));
        $this->assertInstanceOf('\kalanis\RemoteRequest\Protocols\Http', Protocols\AProtocol::getProtocol('https'));
        $this->assertInstanceOf('\kalanis\RemoteRequest\Protocols\Fsp', Protocols\AProtocol::getProtocol('fsp'));
    }

    /**
     * @throws RequestException
     */
    public function testFail(): void
    {
        $this->expectException(RequestException::class);
        Protocols\AProtocol::getProtocol('unknown');
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
