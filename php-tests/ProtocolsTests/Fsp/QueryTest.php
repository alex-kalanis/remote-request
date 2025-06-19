<?php

namespace tests\ProtocolsTests\Fsp;


use tests\CommonTestClass;
use kalanis\RemoteRequest\Protocols\Fsp;


class QueryTest extends CommonTestClass
{
    public function testQueryVersion(): void
    {
        $lib = new Fsp\Query\Version(new Fsp\Query());
        $lib->setKey(258)->setSequence(772);
        $this->assertEquals(stream_get_contents(Query\QueryMock::load()->getRequestVersion()), $lib->compile());
    }

    public function testQueryGetDir(): void
    {
        $lib = new Fsp\Query\GetDir(new Fsp\Query());
        $lib->setKey(258)->setSequence(772);
        $lib->setDirPath('foo/bar1')->setPosition(37);
        $this->assertEquals(stream_get_contents(Query\QueryMock::load()->getRequestGetDir()), $lib->compile());
    }

    public function testQueryGetFile(): void
    {
        $lib = new Fsp\Query\GetFile(new Fsp\Query());
        $lib->setKey(258)->setSequence(772);
        $lib->setFilePath('foo/bar2')->setOffset(2780)->setLimit(728);
        $this->assertEquals(stream_get_contents(Query\QueryMock::load()->getRequestGetFile()), $lib->compile());
    }

    public function testQueryUpload(): void
    {
        $lib = new Fsp\Query\Upload(new Fsp\Query());
        $lib->setKey(258)->setSequence(772);
        $lib->setFilePath('foo/bar3')->setData('ABCDEFGHIJKLMNOPQRSTUVWXYZ012456789abcdefghijklmnopqrstuvwxyz')->setOffset(2780);
        $this->assertEquals(stream_get_contents(Query\QueryMock::load()->getRequestUpload()), $lib->compile());
    }

    public function testQueryInstall(): void
    {
        $lib = new Fsp\Query\Install(new Fsp\Query());
        $lib->setKey(258)->setSequence(772);
        $lib->setFilePath('foo/bar4')->setTimestamp(1234567890);
        $this->assertEquals(stream_get_contents(Query\QueryMock::load()->getRequestInstall()), $lib->compile());
    }

    public function testQueryDelFile(): void
    {
        $lib = new Fsp\Query\DelFile(new Fsp\Query());
        $lib->setKey(258)->setSequence(772);
        $lib->setFilePath('foo/bar5');
        $this->assertEquals(stream_get_contents(Query\QueryMock::load()->getRequestDelFile()), $lib->compile());
    }

    public function testQueryDelDir(): void
    {
        $lib = new Fsp\Query\DelDir(new Fsp\Query());
        $lib->setKey(258)->setSequence(772);
        $lib->setDirPath('foo/bar6');
        $this->assertEquals(stream_get_contents(Query\QueryMock::load()->getRequestDelDir()), $lib->compile());
    }

    public function testQueryGetProtection(): void
    {
        $lib = new Fsp\Query\GetProtection(new Fsp\Query());
        $lib->setKey(258)->setSequence(772);
        $lib->setDirPath('foo/bar7');
        $this->assertEquals(stream_get_contents(Query\QueryMock::load()->getRequestGetProtection()), $lib->compile());
    }

    public function testQuerySetProtection(): void
    {
        $lib = new Fsp\Query\SetProtection(new Fsp\Query());
        $lib->setKey(258)->setSequence(772);
        $lib->setDirPath('foo/bar8')
            ->setOperation(Fsp\Query\SetProtection::CAN_PRESERVE_FILE)
            ->allowOperation(false);
        $this->assertEquals(stream_get_contents(Query\QueryMock::load()->getRequestSetProtection()), $lib->compile());
    }

    public function testQueryMakeDir(): void
    {
        $lib = new Fsp\Query\MakeDir(new Fsp\Query());
        $lib->setKey(258)->setSequence(772);
        $lib->setDirPath('foo/bar9');
        $this->assertEquals(stream_get_contents(Query\QueryMock::load()->getRequestMakeDir()), $lib->compile());
    }

    public function testQueryBye(): void
    {
        $lib = new Fsp\Query\Bye(new Fsp\Query());
        $lib->setKey(258)->setSequence(772);
        $this->assertEquals(stream_get_contents(Query\QueryMock::load()->getRequestBye()), $lib->compile());
    }

    public function testQueryGrabFile(): void
    {
        $lib = new Fsp\Query\GrabFile(new Fsp\Query());
        $lib->setKey(258)->setSequence(772);
        $lib->setFilePath('foo/bar10')->setOffset(2780);
        $this->assertEquals(stream_get_contents(Query\QueryMock::load()->getRequestGrabFile()), $lib->compile());
    }

    public function testQueryGrabDone(): void
    {
        $lib = new Fsp\Query\GrabDone(new Fsp\Query());
        $lib->setKey(258)->setSequence(772);
        $lib->setFilePath('foo/bar11')->setTimestamp(1234567890);
        $this->assertEquals(stream_get_contents(Query\QueryMock::load()->getRequestGrabDone()), $lib->compile());
    }

    public function testQueryStat(): void
    {
        $lib = new Fsp\Query\Stat(new Fsp\Query());
        $lib->setKey(258)->setSequence(772);
        $lib->setFilePath('foo/bar12');
        $this->assertEquals(stream_get_contents(Query\QueryMock::load()->getRequestStat()), $lib->compile());
    }

    public function testQueryRename(): void
    {
        $lib = new Fsp\Query\Rename(new Fsp\Query());
        $lib->setKey(258)->setSequence(772);
        $lib->setFilePath('foo/bar13')->setNewPath('foo/bar14');
        $this->assertEquals(stream_get_contents(Query\QueryMock::load()->getRequestRename()), $lib->compile());
    }
}
