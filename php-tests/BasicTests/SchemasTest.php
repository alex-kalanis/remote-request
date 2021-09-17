<?php

namespace BasicTests;


use CommonTestClass;
use kalanis\RemoteRequest\Protocols;
use kalanis\RemoteRequest\RequestException;
use kalanis\RemoteRequest\Schemas;


class SchemasTest extends CommonTestClass
{
    /**
     * @throws RequestException
     */
    public function testInit(): void
    {
        $this->assertInstanceOf('\kalanis\RemoteRequest\Schemas\File', Schemas\ASchema::getSchema(Schemas\ASchema::SCHEMA_FILE));
        $this->assertInstanceOf('\kalanis\RemoteRequest\Schemas\Php', Schemas\ASchema::getSchema(Schemas\ASchema::SCHEMA_PHP));
        $this->assertInstanceOf('\kalanis\RemoteRequest\Schemas\Tcp', Schemas\ASchema::getSchema(Schemas\ASchema::SCHEMA_TCP));
        $this->assertInstanceOf('\kalanis\RemoteRequest\Schemas\Udp', Schemas\ASchema::getSchema(Schemas\ASchema::SCHEMA_UDP));
        $this->assertInstanceOf('\kalanis\RemoteRequest\Schemas\Ssl', Schemas\ASchema::getSchema(Schemas\ASchema::SCHEMA_SSL));
    }

    public function testSimple(): void
    {
        $schema = new Schemas\Tcp();
        $schema->setTarget('');
        $this->assertEmpty($schema->getHost());
        $this->assertEquals('tcp://', $schema->getHostname());
        $this->assertEquals(SOL_TCP, $schema->getProtocol());
        $schema->setTarget(Schemas\Php::HOST_TEMP);
        $this->assertEquals('tcp://temp', $schema->getHostname());
        $this->assertEquals('temp', $schema->getHost());
        $this->assertEquals(1, $schema->getPort());
        $this->assertEquals(30, $schema->getTimeout());
    }

    public function testHttp(): void
    {
        $schema = new Schemas\Tcp();
        $lineSett = new Protocols\Http\Query();
        $schema->setRequest($lineSett->setHost(Schemas\Php::HOST_MEMORY)->setPort(123456));
        $this->assertEquals('memory', $schema->getHost());
        $this->assertEquals(123456, $schema->getPort());
    }

    public function testUdp(): void
    {
        $schema = new Schemas\Udp();
        $this->assertEquals('udp://', $schema->getHostname());
        $this->assertEquals(SOL_UDP, $schema->getProtocol());
    }

    public function testSsl(): void
    {
        $schema = new Schemas\Ssl();
        $this->assertEquals('ssl://', $schema->getHostname());
        $this->assertEquals(SOL_TCP, $schema->getProtocol());
    }

    public function testPhp(): void
    {
        $schema = new Schemas\Php();
        $this->assertEquals('php://', $schema->getHostname());
        $this->assertEquals(0, $schema->getProtocol());
        $this->assertNull($schema->getPort());
        $this->assertNull($schema->getTimeout());
    }

    public function testFile(): void
    {
        $schema = new Schemas\File();
        $this->assertEquals('file://', $schema->getHostname());
        $this->assertEquals(0, $schema->getProtocol());
        $this->assertNull($schema->getPort());
        $this->assertNull($schema->getTimeout());
    }

    /**
     * @throws RequestException
     */
    public function testFail(): void
    {
        $this->expectException(RequestException::class);
        Schemas\ASchema::getSchema('unknown');
    }
}
