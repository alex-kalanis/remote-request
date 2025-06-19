<?php

namespace tests\BasicTests\Connection;


use kalanis\RemoteRequest\Interfaces;
use kalanis\RemoteRequest\Pointers;


class PointerProcessorMock extends Pointers\Processor
{
    public function processPointer($filePointer, Interfaces\IConnectionParams $params): parent
    {
        $this->checkPointer($filePointer);
        $this->writeRequest($filePointer, $params);
        rewind($filePointer); // FOR REASON
        $this->readResponse($filePointer);
        return $this;
    }
}
