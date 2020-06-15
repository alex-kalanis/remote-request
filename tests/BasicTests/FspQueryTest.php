<?php

use RemoteRequest\Protocols\Fsp;

class FspQueryMock
{
    public static function load(): self
    {
        return new static();
    }

    public function getRequestVersion(): string
    {
        return fspMakeDummyQuery([
            0x10, # CC_VERSION
            0x26, # checksum
            0x01, 0x02, # key
            0x03, 0x04, # sequence
            0x00, 0x00, # data_length
            0x00, 0x00, 0x00, 0x00, # position
            # no content
            # no extra data
        ]);
    }

    public function getRequestGetDir(): string
    {
        return fspMakeDummyQuery([
            0x41, # CC_GET_DIR
            0x6A, # checksum
            0x01, 0x02, # key
            0x03, 0x04, # sequence
            0x00, 0x09, # data_length
            0x00, 0x00, 0x00, 0x25, # position
            'foo/bar1', 0x00 # content
            # no extra data
        ]);
    }

    public function getRequestGetFile(): string
    {
        return fspMakeDummyQuery([
            0x42, # CC_GET_FILE
            0x2F, # checksum
            0x01, 0x02, # key
            0x03, 0x04, # sequence
            0x00, 0x09, # data_length
            0x00, 0x00, 0x0B, 0xDC, # position
            'foo/bar2', 0x00 # content
            # no extra data
        ]);
    }

    public function getRequestUpload(): string
    {
        return fspMakeDummyQuery([
            0x43, # CC_UP_LOAD
            0x01, # checksum
            0x01, 0x02, # key
            0x03, 0x04, # sequence
            0x00, 0x3D, # data_length
            0x00, 0x00, 0x0B, 0xDC, # position
            'ABCDEFGHIJKLMNOPQRSTUVWXYZ012456789abcdefghijklmnopqrstuvwxyz', # content
            'foo/bar3', 0x00, # extra data = file path
        ]);
    }

    public function getRequestInstall(): string
    {
        return fspMakeDummyQuery([
            0x44, # CC_INSTALL
            0x0A, # checksum
            0x01, 0x02, # key
            0x03, 0x04, # sequence
            0x00, 0x09, # data_length
            0x00, 0x00, 0x00, 0x04, # position
            'foo/bar4', 0x00, # content = file path
            0x4A, 0x96, 0x03, 0xD2, # extra data - timestamp
        ]);
    }

    public function getRequestDelFile(): string
    {
        return fspMakeDummyQuery([
            0x45, # CC_DEL_FILE
            0x4D, # checksum
            0x01, 0x02, # key
            0x03, 0x04, # sequence
            0x00, 0x09, # data_length
            0x00, 0x00, 0x00, 0x00, # position
            'foo/bar5', 0x00, # content = file path
            # no extra data
        ]);
    }

    public function getRequestDelDir(): string
    {
        return fspMakeDummyQuery([
            0x46, # CC_DEL_DIR
            0x4F, # checksum
            0x01, 0x02, # key
            0x03, 0x04, # sequence
            0x00, 0x09, # data_length
            0x00, 0x00, 0x00, 0x00, # position
            'foo/bar6', 0x00, # content = file path
            # no extra data
        ]);
    }

    public function getRequestGetProtection(): string
    {
        return fspMakeDummyQuery([
            0x47, # CC_GET_PRO
            0x51, # checksum
            0x01, 0x02, # key
            0x03, 0x04, # sequence
            0x00, 0x09, # data_length
            0x00, 0x00, 0x00, 0x00, # position
            'foo/bar7', 0x00, # content = file path
            # no extra data
        ]);
    }

    public function getRequestSetProtection(): string
    {
        return fspMakeDummyQuery([
            0x48, # CC_SET_PRO
            0x01, # checksum
            0x01, 0x02, # key
            0x03, 0x04, # sequence
            0x00, 0x09, # data_length
            0x00, 0x00, 0x00, 0x02, # position
            'foo/bar8', 0x00, # content = file path
            0x2B, 0x67, # extra data
        ]);
    }

    public function getRequestMakeDir(): string
    {
        return fspMakeDummyQuery([
            0x49, # CC_MAKE_DIR
            0x55, # checksum
            0x01, 0x02, # key
            0x03, 0x04, # sequence
            0x00, 0x09, # data_length
            0x00, 0x00, 0x00, 0x00, # position
            'foo/bar9', 0x00 # content
            # no extra data
        ]);
    }

    public function getRequestBye(): string
    {
        return fspMakeDummyQuery([
            0x4A, # CC_BYE
            0x60, # checksum
            0x01, 0x02, # key
            0x03, 0x04, # sequence
            0x00, 0x00, # data_length
            0x00, 0x00, 0x00, 0x00, # position
            # no content
            # no extra data
        ]);
    }

    public function getRequestGrabFile(): string
    {
        return fspMakeDummyQuery([
            0x4B, # CC_GRAB_FILE
            0x69, # checksum
            0x01, 0x02, # key
            0x03, 0x04, # sequence
            0x00, 0x0A, # data_length
            0x00, 0x00, 0x0B, 0xDC, # position
            'foo/bar10', 0x00 # content
            # no extra data
        ]);
    }

    public function getRequestGrabDone(): string
    {
        return fspMakeDummyQuery([
            0x4C, # CC_GRAB_DONE
            0x42, # checksum
            0x01, 0x02, # key
            0x03, 0x04, # sequence
            0x00, 0x0A, # data_length
            0x00, 0x00, 0x00, 0x04, # position
            'foo/bar11', 0x00, # content = file path
            0x4A, 0x96, 0x03, 0xD2, # extra data - timestamp
        ]);
    }

    public function getRequestStat(): string
    {
        return fspMakeDummyQuery([
            0x4D, # CC_GET_FILE
            0x01, # checksum
            0x01, 0x02, # key
            0x03, 0x04, # sequence
            0x00, 0x0A, # data_length
            0x00, 0x00, 0x00, 0x00, # position
            'foo/bar12', 0x00 # content
            # no extra data
        ]);
    }

    public function getRequestRename(): string
    {
        return fspMakeDummyQuery([
            0x4E, # CC_RENAME
            0x01, # checksum
            0x01, 0x02, # key
            0x03, 0x04, # sequence
            0x00, 0x0A, # data_length
            0x00, 0x00, 0x00, 0x0A, # position
            'foo/bar13', 0x00, # content = current file path
            'foo/bar14', 0x00, # extra data = new file path
        ]);
    }
}

class FspQueryTest extends CommonTestClass
{
    public function testQueryVersion()
    {
        $lib = new Fsp\Query\Version(new Fsp\Query());
        $lib->setKey(258)->setSequence(772);
        $this->assertEquals(FspQueryMock::load()->getRequestVersion(), $lib->compile());
    }

    public function testQueryGetDir()
    {
        $lib = new Fsp\Query\GetDir(new Fsp\Query());
        $lib->setKey(258)->setSequence(772);
        $lib->setDirPath('foo/bar1')->setPosition(37);
        $this->assertEquals(FspQueryMock::load()->getRequestGetDir(), $lib->compile());
    }

    public function testQueryGetFile()
    {
        $lib = new Fsp\Query\GetFile(new Fsp\Query());
        $lib->setKey(258)->setSequence(772);
        $lib->setFilePath('foo/bar2')->setOffset(2780);
        $this->assertEquals(FspQueryMock::load()->getRequestGetFile(), $lib->compile());
    }

    public function testQueryUpload()
    {
        $lib = new Fsp\Query\Upload(new Fsp\Query());
        $lib->setKey(258)->setSequence(772);
        $lib->setFilePath('foo/bar3')->setData('ABCDEFGHIJKLMNOPQRSTUVWXYZ012456789abcdefghijklmnopqrstuvwxyz')->setOffset(2780);
        $this->assertEquals(FspQueryMock::load()->getRequestUpload(), $lib->compile());
    }

    public function testQueryInstall()
    {
        $lib = new Fsp\Query\Install(new Fsp\Query());
        $lib->setKey(258)->setSequence(772);
        $lib->setFilePath('foo/bar4')->setTimestamp(1234567890);
        $this->assertEquals(FspQueryMock::load()->getRequestInstall(), $lib->compile());
    }

    public function testQueryDelFile()
    {
        $lib = new Fsp\Query\DelFile(new Fsp\Query());
        $lib->setKey(258)->setSequence(772);
        $lib->setFilePath('foo/bar5');
        $this->assertEquals(FspQueryMock::load()->getRequestDelFile(), $lib->compile());
    }

    public function testQueryDelDir()
    {
        $lib = new Fsp\Query\DelDir(new Fsp\Query());
        $lib->setKey(258)->setSequence(772);
        $lib->setDirPath('foo/bar6');
        $this->assertEquals(FspQueryMock::load()->getRequestDelDir(), $lib->compile());
    }

    public function testQueryGetProtection()
    {
        $lib = new Fsp\Query\GetProtection(new Fsp\Query());
        $lib->setKey(258)->setSequence(772);
        $lib->setDirPath('foo/bar7');
        $this->assertEquals(FspQueryMock::load()->getRequestGetProtection(), $lib->compile());
    }

    public function testQuerySetProtection()
    {
        $lib = new Fsp\Query\SetProtection(new Fsp\Query());
        $lib->setKey(258)->setSequence(772);
        $lib->setDirPath('foo/bar8')
            ->setOperation(Fsp\Query\SetProtection::CAN_PRESERVE_FILE)
            ->allowOperation(false);
        $this->assertEquals(FspQueryMock::load()->getRequestSetProtection(), $lib->compile());
    }

    public function testQueryMakeDir()
    {
        $lib = new Fsp\Query\MakeDir(new Fsp\Query());
        $lib->setKey(258)->setSequence(772);
        $lib->setDirPath('foo/bar9');
        $this->assertEquals(FspQueryMock::load()->getRequestMakeDir(), $lib->compile());
    }

    public function testQueryBye()
    {
        $lib = new Fsp\Query\Bye(new Fsp\Query());
        $lib->setKey(258)->setSequence(772);
        $this->assertEquals(FspQueryMock::load()->getRequestBye(), $lib->compile());
    }

    public function testQueryGrabFile()
    {
        $lib = new Fsp\Query\GrabFile(new Fsp\Query());
        $lib->setKey(258)->setSequence(772);
        $lib->setFilePath('foo/bar10')->setOffset(2780);
        $this->assertEquals(FspQueryMock::load()->getRequestGrabFile(), $lib->compile());
    }

    public function testQueryGrabDone()
    {
        $lib = new Fsp\Query\GrabDone(new Fsp\Query());
        $lib->setKey(258)->setSequence(772);
        $lib->setFilePath('foo/bar11')->setTimestamp(1234567890);
        $this->assertEquals(FspQueryMock::load()->getRequestGrabDone(), $lib->compile());
    }

    public function testQueryStat()
    {
        $lib = new Fsp\Query\Stat(new Fsp\Query());
        $lib->setKey(258)->setSequence(772);
        $lib->setFilePath('foo/bar12');
        $this->assertEquals(FspQueryMock::load()->getRequestStat(), $lib->compile());
    }

    public function testQueryRename()
    {
        $lib = new Fsp\Query\Rename(new Fsp\Query());
        $lib->setKey(258)->setSequence(772);
        $lib->setFilePath('foo/bar13')->setNewPath('foo/bar14');
        $this->assertEquals(FspQueryMock::load()->getRequestRename(), $lib->compile());
    }
}