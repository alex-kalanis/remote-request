<?php

namespace tests\ProtocolsTests\Fsp\Session;


use kalanis\RemoteRequest\Protocols\Fsp;


class SequenceMock extends Fsp\Session\Sequence
{
    protected function getRandInitial(): int
    {
        return 75;
    }
}
