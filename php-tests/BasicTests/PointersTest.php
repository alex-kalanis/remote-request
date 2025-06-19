<?php

namespace tests\BasicTests;


use tests\CommonTestClass;
use kalanis\RemoteRequest\Connection;
use kalanis\RemoteRequest\Protocols;
use kalanis\RemoteRequest\RequestException;


class PointersTest extends CommonTestClass
{
    /**
     * When it blows
     * @throws RequestException
     */
    public function testCallException(): void
    {
        $processor = new Connection\Processor(new Pointers\ExceptionTestSocket());
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
        $processor = new Connection\Processor(new Pointers\EmptyTestSocket());
        $processor->setConnectionParams(new Connection\Params\File());
        $processor->setData(new Protocols\Dummy\Query());
        $this->expectException(RequestException::class);
        $processor->process(); // die
    }
}
