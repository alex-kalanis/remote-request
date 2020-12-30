<?php

namespace ProtocolsTests;


use CommonTestClass;
use RemoteRequest\Protocols;
use RemoteRequest\RequestException;
use RemoteRequest\Schemas;


class LoadersTest extends CommonTestClass
{
    /**
     * @throws RequestException
     */
    public function testInit(): void
    {
        $this->assertInstanceOf('\RemoteRequest\Protocols\Tcp', Protocols\AProtocol::getProtocol(Schemas\ASchema::SCHEMA_TCP));
        $this->assertInstanceOf('\RemoteRequest\Protocols\Tcp', Protocols\AProtocol::getProtocol(Schemas\ASchema::SCHEMA_FILE));
        $this->assertInstanceOf('\RemoteRequest\Protocols\Udp', Protocols\AProtocol::getProtocol(Schemas\ASchema::SCHEMA_UDP));
        $this->assertInstanceOf('\RemoteRequest\Protocols\Http', Protocols\AProtocol::getProtocol('http'));
        $this->assertInstanceOf('\RemoteRequest\Protocols\Http', Protocols\AProtocol::getProtocol('https'));
        $this->assertInstanceOf('\RemoteRequest\Protocols\Fsp', Protocols\AProtocol::getProtocol('fsp'));
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
        $this->assertInstanceOf('\RemoteRequest\Schemas\Tcp', $protocol->getTarget());
        $this->assertInstanceOf('\RemoteRequest\Protocols\Dummy\Query', $protocol->getQuery());
    }

    public function testUdp(): void
    {
        $protocol = new Protocols\Udp();
        $this->assertInstanceOf('\RemoteRequest\Schemas\Udp', $protocol->getTarget());
        $this->assertInstanceOf('\RemoteRequest\Protocols\Dummy\Query', $protocol->getQuery());
    }

    public function testHttp(): void
    {
        $protocol = new Protocols\Http();
        $this->assertInstanceOf('\RemoteRequest\Schemas\Tcp', $protocol->getTarget());
        $this->assertInstanceOf('\RemoteRequest\Protocols\Http\Query', $protocol->getQuery());
    }

    public function testHttps(): void
    {
        $protocol = new Protocols\Https();
        $this->assertInstanceOf('\RemoteRequest\Schemas\Ssl', $protocol->getTarget());
        $this->assertInstanceOf('\RemoteRequest\Protocols\Http\Query', $protocol->getQuery());
    }

    public function testRestful(): void
    {
        $protocol = new Protocols\Restful();
        $this->assertInstanceOf('\RemoteRequest\Schemas\Tcp', $protocol->getTarget());
        $this->assertInstanceOf('\RemoteRequest\Protocols\Restful\Query', $protocol->getQuery());
    }

    public function testFsp(): void
    {
        $protocol = new Protocols\Fsp();
        $this->assertInstanceOf('\RemoteRequest\Schemas\Udp', $protocol->getTarget());
        $this->assertInstanceOf('\RemoteRequest\Protocols\Fsp\Query', $protocol->getQuery());
    }
}