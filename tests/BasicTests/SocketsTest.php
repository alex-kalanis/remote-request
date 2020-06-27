<?php

namespace BasicTests;

use CommonTestClass;
use RemoteRequest\Sockets;

class SocketsTest extends CommonTestClass
{
    public function testInit(): void
    {
        $this->assertInstanceOf('\RemoteRequest\Sockets\SharedInternal', Sockets\ASocket::getPointer(Sockets\ASocket::SOCKET_INTERNAL));
        $this->assertInstanceOf('\RemoteRequest\Sockets\Stream', Sockets\ASocket::getPointer(Sockets\ASocket::SOCKET_STREAM));
        $this->assertInstanceOf('\RemoteRequest\Sockets\Socket', Sockets\ASocket::getPointer(Sockets\ASocket::SOCKET_SOCKET));
        $this->assertInstanceOf('\RemoteRequest\Sockets\FSocket', Sockets\ASocket::getPointer(Sockets\ASocket::SOCKET_FSOCKET));
        $this->assertInstanceOf('\RemoteRequest\Sockets\PfSocket', Sockets\ASocket::getPointer(Sockets\ASocket::SOCKET_PFSOCKET));
    }

    public function testSetSocket(): void
    {
        $pointer = new Sockets\Socket();
        $this->assertInstanceOf('\RemoteRequest\Sockets\Socket', $pointer);
    }

    public function testSetFsocket(): void
    {
        $pointer = new Sockets\FSocket();
        $this->assertInstanceOf('\RemoteRequest\Sockets\FSocket', $pointer);
    }

    public function testSetPfsocket(): void
    {
        $pointer = new Sockets\PfSocket();
        $this->assertInstanceOf('\RemoteRequest\Sockets\PfSocket', $pointer);
    }

    public function testSetStream(): void
    {
        $pointer = new Sockets\Stream();
        $pointer->setContextOptions(['foo' => 'bar', 'baz' => 'eff']);
        $this->assertInstanceOf('\RemoteRequest\Sockets\Stream', $pointer);
    }

    public function testSetSharedInternal(): void
    {
        $pointer = new Sockets\SharedInternal();
        $this->assertInstanceOf('\RemoteRequest\Sockets\SharedInternal', $pointer);
    }
}