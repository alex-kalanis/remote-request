<?php

namespace tests\ProtocolsTests;


use tests\CommonTestClass;
use kalanis\RemoteRequest\Connection;
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
        $this->assertInstanceOf(Protocols\Tcp::class, Protocols\Factory::getProtocol(Interfaces\ISchema::SCHEMA_TCP, $lang));
        $this->assertInstanceOf(Protocols\Tcp::class, Protocols\Factory::getProtocol(Interfaces\ISchema::SCHEMA_FILE, $lang));
        $this->assertInstanceOf(Protocols\Udp::class, Protocols\Factory::getProtocol(Interfaces\ISchema::SCHEMA_UDP, $lang));
        $this->assertInstanceOf(Protocols\Http::class, Protocols\Factory::getProtocol( 'http', $lang));
        $this->assertInstanceOf(Protocols\Http::class, Protocols\Factory::getProtocol('https', $lang));
        $this->assertInstanceOf(Protocols\Fsp::class, Protocols\Factory::getProtocol('fsp', $lang));
//        $this->assertInstanceOf(Protocols\Http2::class, Protocols\Factory::getProtocol('http2', $lang));
//        $this->assertInstanceOf(Protocols\Http3::class, Protocols\Factory::getProtocol('http3', $lang));
        $this->assertInstanceOf(Protocols\WebDAV::class, Protocols\Factory::getProtocol('webdav', $lang));
//        $this->assertInstanceOf(Protocols\Samba::class, Protocols\Factory::getProtocol('smb', $lang));
//        $this->assertInstanceOf(Protocols\Git::class, Protocols\Factory::getProtocol('git', $lang));
    }

    /**
     * @throws RequestException
     */
    public function testFail(): void
    {
        $this->expectException(RequestException::class);
        Protocols\Factory::getProtocol('unknown', new Translations());
    }

    public function testTcp(): void
    {
        $protocol = new Protocols\Tcp();
        $this->assertInstanceOf(Connection\Params\Tcp::class, $protocol->getParams());
        $this->assertInstanceOf(Protocols\Dummy\Query::class, $protocol->getQuery());
    }

    public function testUdp(): void
    {
        $protocol = new Protocols\Udp();
        $this->assertInstanceOf(Connection\Params\Udp::class, $protocol->getParams());
        $this->assertInstanceOf(Protocols\Dummy\Query::class, $protocol->getQuery());
    }

    public function testHttp(): void
    {
        $protocol = new Protocols\Http([], true);
        $this->assertInstanceOf(Connection\Params\Tcp::class, $protocol->getParams());
        $this->assertInstanceOf(Protocols\Http\Query::class, $protocol->getQuery());
    }

    public function testHttps(): void
    {
        $protocol = new Protocols\Https([
            'ssl' => [
//                'verify_peer' => !DEVEL_ENVIRONMENT, // You could skip all of the trouble by changing this to false, but it's WAY uncool for security reasons. // kecy...
//                'cafile' => '/etc/ssl/certs/cacert.pem',
//                'CN_match' => 'example.com', // Change this to your certificates Common Name (or just comment this line out if not needed)
//                'ciphers' => 'HIGH:!SSLv2:!SSLv3',
                'disable_compression' => true,
            ],
        ]);
        $this->assertInstanceOf(Connection\Params\Ssl::class, $protocol->getParams());
        $this->assertInstanceOf(Protocols\Http\Query::class, $protocol->getQuery());
    }

    public function testRestful(): void
    {
        $protocol = new Protocols\Restful();
        $this->assertInstanceOf(Connection\Params\Tcp::class, $protocol->getParams());
        $this->assertInstanceOf(Protocols\Restful\Query::class, $protocol->getQuery());
    }

    public function testWebDav(): void
    {
        $protocol = new Protocols\WebDAV();
        $this->assertInstanceOf(Connection\Params\Tcp::class, $protocol->getParams());
        $this->assertInstanceOf(Protocols\WebDAV\Query::class, $protocol->getQuery());
    }

    public function testFsp(): void
    {
        $protocol = new Protocols\Fsp();
        $this->assertInstanceOf(Connection\Params\Udp::class, $protocol->getParams());
        $this->assertInstanceOf(Protocols\Fsp\Query::class, $protocol->getQuery());
    }
}
