<?php

namespace tests\BasicTests\Connection;


use kalanis\RemoteRequest\Connection;
use kalanis\RemoteRequest\Interfaces;
use kalanis\RemoteRequest\Sockets;


class ConnectProcessorMock extends Connection\Processor
{
    public function __construct(?Sockets\ASocket $method = null, ?Interfaces\IRRTranslations $lang = null)
    {
        parent::__construct($method, $lang);
        $this->processor = new PointerProcessorMock($lang);
    }
}
