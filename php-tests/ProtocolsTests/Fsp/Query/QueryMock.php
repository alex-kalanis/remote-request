<?php

namespace tests\ProtocolsTests\Fsp\Query;


use tests\ProtocolsTests\Fsp\Common;


class QueryMock
{
    public static function load(): self
    {
        return new static();
    }

    public function getRequestVersion()
    {
        return Common::makeDummyQuery([
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

    public function getRequestGetDir()
    {
        return Common::makeDummyQuery([
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

    public function getRequestGetFile()
    {
        return Common::makeDummyQuery([
            0x42, # CC_GET_FILE
            0x0D, # checksum
            0x01, 0x02, # key
            0x03, 0x04, # sequence
            0x00, 0x09, # data_length
            0x00, 0x00, 0x0B, 0xDC, # position
            'foo/bar2', 0x00, # content
            0x03, 0xd8, # extra data
        ]);
    }

    public function getRequestUpload()
    {
        return Common::makeDummyQuery([
            0x43, # CC_UP_LOAD
            0x8F, # checksum
            0x01, 0x02, # key
            0x03, 0x04, # sequence
            0x00, 0x3D, # data_length
            0x00, 0x00, 0x0B, 0xDC, # position
            'ABCDEFGHIJKLMNOPQRSTUVWXYZ012456789abcdefghijklmnopqrstuvwxyz', # content
            'foo/bar3', 0x00, # extra data = file path
        ]);
    }

    public function getRequestInstall()
    {
        return Common::makeDummyQuery([
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

    public function getRequestDelFile()
    {
        return Common::makeDummyQuery([
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

    public function getRequestDelDir()
    {
        return Common::makeDummyQuery([
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

    public function getRequestGetProtection()
    {
        return Common::makeDummyQuery([
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

    public function getRequestSetProtection()
    {
        return Common::makeDummyQuery([
            0x48, # CC_SET_PRO
            0xE9, # checksum
            0x01, 0x02, # key
            0x03, 0x04, # sequence
            0x00, 0x09, # data_length
            0x00, 0x00, 0x00, 0x02, # position
            'foo/bar8', 0x00, # content = file path
            0x2B, 0x67, # extra data
        ]);
    }

    public function getRequestMakeDir()
    {
        return Common::makeDummyQuery([
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

    public function getRequestBye()
    {
        return Common::makeDummyQuery([
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

    public function getRequestGrabFile()
    {
        return Common::makeDummyQuery([
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

    public function getRequestGrabDone()
    {
        return Common::makeDummyQuery([
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

    public function getRequestStat()
    {
        return Common::makeDummyQuery([
            0x4D, # CC_GET_FILE
            0x85, # checksum
            0x01, 0x02, # key
            0x03, 0x04, # sequence
            0x00, 0x0A, # data_length
            0x00, 0x00, 0x00, 0x00, # position
            'foo/bar12', 0x00 # content
            # no extra data
        ]);
    }

    public function getRequestRename()
    {
        return Common::makeDummyQuery([
            0x4E, # CC_RENAME
            0xAB, # checksum
            0x01, 0x02, # key
            0x03, 0x04, # sequence
            0x00, 0x0A, # data_length
            0x00, 0x00, 0x00, 0x0A, # position
            'foo/bar13', 0x00, # content = current file path
            'foo/bar14', 0x00, # extra data = new file path
        ]);
    }
}
