<?php

namespace tests\ProtocolsTests\Fsp\Protocol;


use tests\ProtocolsTests\Fsp\Common;


class ProtocolQueryMock
{
    /**
     * What we send into server
     * @return resource|null
     */
    public function getRequestSimple()
    {
        return Common::makeDummyQuery([
            0x41, # CC_GET_DIR
            0x7f, # checksum
            0x01, 0x02, # key
            0x03, 0x04, # sequence
            0x00, 0x05, # data_length
            0x00, 0x00, 0x00, 0x00, # position
            'DATA', 0x00, # content "DATA"
            0x01, 0x00, # xtra
        ]);
    }

    /**
     * What we send into server
     * @return resource|null
     */
    public function getRequestFailedChk()
    {
        return Common::makeDummyQuery([
            0x41, # CC_GET_DIR
            0xAC, # checksum
            0x01, 0x02, # key
            0x03, 0x04, # sequence
            0x00, 0x05, # data_length
            0x00, 0x00, 0x00, 0x00, # position
            'DATA', 0x00, # content "DATA"
            0x01, 0x00, # xtra
        ]);
    }
}
