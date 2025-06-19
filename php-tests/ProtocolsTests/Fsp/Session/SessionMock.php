<?php

namespace tests\ProtocolsTests\Fsp\Session;


use kalanis\RemoteRequest\Protocols\Fsp;


class SessionMock extends Fsp\Session
{
    protected function getRandInitial(): int
    {
        return 64;
    }

    protected function sequencer($withInit = true): Fsp\Session\Sequence
    {
        $lib = new SequenceMock($this->getRRLang());
        if ($withInit) {
            $lib->generateSequence();
        }
        return $lib;
    }
}
