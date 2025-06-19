<?php

namespace tests\ProtocolsTests\Http\Answer;


use kalanis\RemoteRequest\Protocols\Http;


class XAnswer extends Http\Answer
{
    protected int $seekSize = 20; // in how big block we will look for delimiters
    protected int $seekPos = 15; // must be reasonably lower than seekSize - because it's necessary to find delimiters even on edges
    protected int $maxHeaderSize = 200; // die early in stream
    protected int $maxStringSize = 100; // pass into stream
}
