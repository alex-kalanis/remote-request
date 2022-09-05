<?php

namespace BasicTests;


use CommonTestClass;
use kalanis\RemoteRequest\Connection;
use kalanis\RemoteRequest\Interfaces;
use kalanis\RemoteRequest\Protocols;
use kalanis\RemoteRequest\RequestException;
use kalanis\RemoteRequest\Sockets;
use kalanis\RemoteRequest\Translations;


class EmptyTestSocket extends Sockets\ASocket
{
    protected function remotePointer(Interfaces\IConnectionParams $schema)
    {
        return null;
    }
}


class ExceptionTestSocket extends Sockets\ASocket
{
    protected function remotePointer(Interfaces\IConnectionParams $schema)
    {
        throw new RequestException($this->lang->rrSocketCannotConnect());
    }
}


class PointersTest extends CommonTestClass
{
    /**
     * When it blows
     * @throws RequestException
     */
    public function testCallException(): void
    {
        $lang = new Translations();
        $processor = new Connection\Processor($lang, new ExceptionTestSocket($lang));
        $processor->setConnectionParams(new Connection\Params\File());
        $processor->setData(new Protocols\Dummy\Query());
        $this->expectException(RequestException::class);
        $processor->process(); // die
    }

    /**
     * When it blows
     * @throws RequestException
     */
    public function testCallNoPointer(): void
    {
        $lang = new Translations();
        $processor = new Connection\Processor($lang, new EmptyTestSocket($lang));
        $processor->setConnectionParams(new Connection\Params\File());
        $processor->setData(new Protocols\Dummy\Query());
        $this->expectException(RequestException::class);
        $processor->process(); // die
    }
}
