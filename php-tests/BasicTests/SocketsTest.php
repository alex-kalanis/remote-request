<?php

namespace BasicTests;


use CommonTestClass;
use kalanis\RemoteRequest\Interfaces;
use kalanis\RemoteRequest\Sockets;


class SocketsTest extends CommonTestClass
{
    public function testInit(): void
    {
        $this->assertInstanceOf(Sockets\SharedInternal::class, Sockets\Factory::getPointer(Interfaces\ISocket::SOCKET_INTERNAL));
        $this->assertInstanceOf(Sockets\Stream::class, Sockets\Factory::getPointer(Interfaces\ISocket::SOCKET_STREAM));
        $this->assertInstanceOf(Sockets\Socket::class, Sockets\Factory::getPointer(Interfaces\ISocket::SOCKET_SOCKET));
        $this->assertInstanceOf(Sockets\FSocket::class, Sockets\Factory::getPointer(Interfaces\ISocket::SOCKET_FSOCKET));
        $this->assertInstanceOf(Sockets\PfSocket::class, Sockets\Factory::getPointer(Interfaces\ISocket::SOCKET_PFSOCKET));
    }

    public function testSetSocket(): void
    {
        $pointer = new Sockets\Socket();
        $this->assertInstanceOf(Sockets\Socket::class, $pointer);
    }

    public function testSetFsocket(): void
    {
        $pointer = new Sockets\FSocket();
        $this->assertInstanceOf(Sockets\FSocket::class, $pointer);
    }

    public function testSetPfsocket(): void
    {
        $pointer = new Sockets\PfSocket();
        $this->assertInstanceOf(Sockets\PfSocket::class, $pointer);
    }

    public function testSetStream(): void
    {
        $pointer = new Sockets\Stream();
        $pointer->setContextOptions(['foo' => 'bar', 'baz' => 'eff']);
        $this->assertInstanceOf(Sockets\Stream::class, $pointer);
    }

    public function testSetSharedInternal(): void
    {
        $pointer = new Sockets\SharedInternal();
        $this->assertInstanceOf(Sockets\SharedInternal::class, $pointer);
    }
}
