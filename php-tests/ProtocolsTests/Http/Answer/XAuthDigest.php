<?php

namespace tests\ProtocolsTests\Http\Answer;


use kalanis\RemoteRequest\Protocols\Http;


class XAuthDigest extends Http\Answer\AuthDigest
{
    protected int $maxStringSize = 100; // pass into stream
}
