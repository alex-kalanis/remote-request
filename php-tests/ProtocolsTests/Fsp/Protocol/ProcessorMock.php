<?php

namespace tests\ProtocolsTests\Fsp\Protocol;


use kalanis\RemoteRequest\Connection;
use tests\ProtocolsTests\Fsp\Common;


class ProcessorMock extends Connection\Processor
{
    /**
     * What server responds
     * @return resource|null
     */
    public function getResponseSimple()
    {
        return Common::makeDummyQuery([
            0x42, # CC_GET_FILE
            0x8b, # checksum - 139
            0x01, 0x02, # key
            0x03, 0x04, # sequence
            0x00, 0x04, # data_length
            0x00, 0x00, 0x00, 0x20, # position
            'DATA', # content "DATA"
            # no xtra
        ]);
    }

    /**
     * What server responds
     * @return resource|null
     */
    public function getResponseReal()
    {
        return Common::makeDummyQuery([
            0x10, # CC_VERSION
            0x1B, # checksum 27
            0xF5, 0x8C, # key
            0x00, 0x10, # sequence
            0x00, 0x0E, # data_length
            0x00, 0x00, 0x00, 0x03, # position
            0x66, 0x73, 0x70, 0x64, 0x20, 0x32, 0x2E, 0x38, 0x2E, 0x31, 0x62, 0x32, 0x39, 0x00, # content "fspd 2.8.1b29"
            0x21, 0x05, 0xAC, # xtra
        ]);
    }

    /**
     * What server should not respond
     * @return resource|null
     */
    public function getResponseFailedChk()
    {
        return Common::makeDummyQuery([
            0x42, # CC_GET_FILE
            0x9A, # checksum - fail!
            0x01, 0x02, # key
            0x03, 0x04, # sequence
            0x00, 0x04, # data_length
            0x00, 0x00, 0x00, 0x20, # position
            'DATA', # content "DATA"
        ]);
    }

    public function getResponseShort()
    {
        return Common::makeDummyQuery([
            0x81, # CC_TEST
            0x31, # checksum
            0x01, 0x02, # key
        ]);
    }

    public function getResponseLong()
    {
        return Common::makeDummyQuery([
            0x81, # CC_TEST
            0x0B, # checksum
            0x01, 0x02, # key
            0x03, 0x04, # sequence
            0xff, 0xff, # data_length
            0x00, 0x00, 0x00, 0x00, # position
            str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', 29), # shitty content
            # no extra data
        ]);
    }
}
