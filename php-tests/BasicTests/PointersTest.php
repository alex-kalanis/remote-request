<?php

namespace BasicTests;


use CommonTestClass;
use kalanis\RemoteRequest\Connection;
use kalanis\RemoteRequest\Protocols;
use kalanis\RemoteRequest\RequestException;
use kalanis\RemoteRequest\Schemas;
use kalanis\RemoteRequest\Sockets;
use kalanis\RemoteRequest\Translations;


class EmptyTestSocket extends Sockets\ASocket
{
    protected function remotePointer(Schemas\ASchema $protocolWrapper)
    {
        return null;
    }
}


class ExceptionTestSocket extends Sockets\ASocket
{
    protected function remotePointer(Schemas\ASchema $protocolWrapper)
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
        $processor->setProtocolSchema(new Schemas\File());
        $processor->setData(new Protocols\Dummy\Query());
        $this->expectException(RequestException::class);
        $processor->getResponse(); // die
    }

    /**
     * When it blows
     * @throws RequestException
     */
    public function testCallNoPointer(): void
    {
        $lang = new Translations();
        $processor = new Connection\Processor($lang, new EmptyTestSocket($lang));
        $processor->setProtocolSchema(new Schemas\File());
        $processor->setData(new Protocols\Dummy\Query());
        $this->expectException(RequestException::class);
        $processor->getResponse(); // die
    }
}
