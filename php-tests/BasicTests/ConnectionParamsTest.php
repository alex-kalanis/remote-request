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
        $this->assertInstanceOf('\kalanis\RemoteRequest\Connection\Params\File', Connection\Params\Factory::getForSchema($lang, ISchema::SCHEMA_FILE));
        $this->assertInstanceOf('\kalanis\RemoteRequest\Connection\Params\Php', Connection\Params\Factory::getForSchema($lang, ISchema::SCHEMA_PHP));
        $this->assertInstanceOf('\kalanis\RemoteRequest\Connection\Params\Tcp', Connection\Params\Factory::getForSchema($lang, ISchema::SCHEMA_TCP));
        $this->assertInstanceOf('\kalanis\RemoteRequest\Connection\Params\Udp', Connection\Params\Factory::getForSchema($lang, ISchema::SCHEMA_UDP));
        $this->assertInstanceOf('\kalanis\RemoteRequest\Connection\Params\Ssl', Connection\Params\Factory::getForSchema($lang, ISchema::SCHEMA_SSL));
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
        Connection\Params\Factory::getForSchema(new Translations(), 'unknown');
    }
}
