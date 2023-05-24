<?php

namespace BasicTests;


use CommonTestClass;
use kalanis\RemoteRequest\Connection;
use kalanis\RemoteRequest\Interfaces\ISchema;
use kalanis\RemoteRequest\Protocols;
use kalanis\RemoteRequest\RequestException;
use kalanis\RemoteRequest\Translations;


class ConnectionParamsTest extends CommonTestClass
{
    /**
     * @throws RequestException
     */
    public function testInit(): void
    {
        $lang = new Translations();
        $this->assertInstanceOf(Connection\Params\File::class, Connection\Params\Factory::getForSchema(ISchema::SCHEMA_FILE, $lang));
        $this->assertInstanceOf(Connection\Params\Php::class, Connection\Params\Factory::getForSchema(ISchema::SCHEMA_PHP, $lang));
        $this->assertInstanceOf(Connection\Params\Tcp::class, Connection\Params\Factory::getForSchema(ISchema::SCHEMA_TCP, $lang));
        $this->assertInstanceOf(Connection\Params\Udp::class, Connection\Params\Factory::getForSchema(ISchema::SCHEMA_UDP, $lang));
        $this->assertInstanceOf(Connection\Params\Ssl::class, Connection\Params\Factory::getForSchema(ISchema::SCHEMA_SSL, $lang));
    }

    public function testSimple(): void
    {
        $schema = new Connection\Params\Tcp();
        $schema->setTarget('');
        $this->assertEmpty($schema->getHost());
        $this->assertEquals('tcp://', $schema->getSchema());
        $this->assertEquals(SOL_TCP, $schema->getProtocolVersion());
        $schema->setTarget(Connection\Params\Php::HOST_TEMP);
        $this->assertEquals('tcp://', $schema->getSchema());
        $this->assertEquals('temp', $schema->getHost());
        $this->assertEquals(1, $schema->getPort());
        $this->assertEquals(30, $schema->getTimeout());
    }

    public function testHttp(): void
    {
        $schema = new Connection\Params\Tcp();
        $lineSett = new Protocols\Http\Query();
        $schema->setRequest($lineSett->setHost(Connection\Params\Php::HOST_MEMORY)->setPort(123456));
        $this->assertEquals('memory', $schema->getHost());
        $this->assertEquals(123456, $schema->getPort());
    }

    public function testUdp(): void
    {
        $schema = new Connection\Params\Udp();
        $this->assertEquals('udp://', $schema->getSchema());
        $this->assertEquals(SOL_UDP, $schema->getProtocolVersion());
    }

    public function testSsl(): void
    {
        $schema = new Connection\Params\Ssl();
        $this->assertEquals('ssl://', $schema->getSchema());
        $this->assertEquals(SOL_TCP, $schema->getProtocolVersion());
    }

    public function testPhp(): void
    {
        $schema = new Connection\Params\Php();
        $this->assertEquals('php://', $schema->getSchema());
        $this->assertEquals(0, $schema->getProtocolVersion());
        $this->assertNull($schema->getPort());
        $this->assertNull($schema->getTimeout());
    }

    public function testFile(): void
    {
        $schema = new Connection\Params\File();
        $this->assertEquals('file://', $schema->getSchema());
        $this->assertEquals(0, $schema->getProtocolVersion());
        $this->assertNull($schema->getPort());
        $this->assertNull($schema->getTimeout());
    }

    /**
     * @throws RequestException
     */
    public function testFail(): void
    {
        $this->expectException(RequestException::class);
        Connection\Params\Factory::getForSchema('unknown', new Translations());
    }
}
