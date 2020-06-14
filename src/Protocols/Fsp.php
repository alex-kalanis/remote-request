<?php

namespace RemoteRequest\Protocols;

use RemoteRequest;

/**
 * Properties for query to remote server - method FSP
 * @link http://fsp.sourceforge.net
 * @link http://fsp.sourceforge.net/doc/PROTOCOL.txt
 * @link https://sourceforge.net/p/fsp/code/ci/master/tree/doc/PROTOCOL
 */
class Fsp extends Udp
{
    # how large is header
    const HEADER_SIZE = 12;

    # Command Codes
    const CC_VERSION   = 0x10;
    const CC_ERR       = 0x40;
    const CC_GET_DIR   = 0x41;
    const CC_GET_FILE  = 0x42;
    const CC_UP_LOAD   = 0x43;
    const CC_INSTALL   = 0x44;
    const CC_DEL_FILE  = 0x45;
    const CC_DEL_DIR   = 0x46;
    const CC_GET_PRO   = 0x47;
    const CC_SET_PRO   = 0x48;
    const CC_GRAB_FILE = 0x48;
    const CC_MAKE_DIR  = 0x49;
    const CC_BYE       = 0x4A;
    const CC_GRAB_DONE = 0x4C;
    const CC_STAT      = 0x4D;
    const CC_RENAME    = 0x4E;
    const CC_CH_PASSW  = 0x4F;
    const CC_LIMIT     = 0x80;
    const CC_TEST      = 0x81;

    # RDIRENT Type Codes - directory blocks
    const RDTYPE_END    = 0x00;
    const RDTYPE_FILE   = 0x01;
    const RDTYPE_DIR    = 0x02;
    const RDTYPE_SKIP   = 0x2a;

//    const FLAG_VERSION_LOGGING = 1;
//    const FLAG_VERSION_READ_ONLY = 2;
//    const FLAG_VERSION_REVERSE_LOOKUP = 4;
//    const FLAG_VERSION_PRIVATE = 8;
//    const FLAG_VERSION_THRUPUT = 16;
//    const FLAG_VERSION_ACCEPT_XTRA = 32;

    protected function loadQuery(): RemoteRequest\Protocols\Dummy\Query
    {
        return new Fsp\Query();
    }

    protected function loadAnswer(): RemoteRequest\Protocols\Dummy\Answer
    {
        return new Fsp\Answer();
    }
}
