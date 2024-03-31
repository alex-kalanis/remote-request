<?php

namespace kalanis\RemoteRequest\Protocols;


use kalanis\RemoteRequest;


/**
 * Class Fsp
 * @package kalanis\RemoteRequest\Protocols
 * Properties for query to remote server - method FSP
 * @link http://fsp.sourceforge.net
 * @link https://sourceforge.net/p/fsp/code/ci/master/tree/doc/PROTOCOL
 */
class Fsp extends Udp
{
    # how large is whole load
    public const HEADER_SIZE = 12;
    public const SPACE_SIZE = 1024;
    public const MAX_PACKET_SIZE = 1036; // 1024 + 12

    # Command Codes
    public const CC_VERSION   = 0x10;
    public const CC_ERR       = 0x40;
    public const CC_GET_DIR   = 0x41;
    public const CC_GET_FILE  = 0x42;
    public const CC_UP_LOAD   = 0x43;
    public const CC_INSTALL   = 0x44;
    public const CC_DEL_FILE  = 0x45;
    public const CC_DEL_DIR   = 0x46;
    public const CC_GET_PRO   = 0x47;
    public const CC_SET_PRO   = 0x48;
    public const CC_MAKE_DIR  = 0x49;
    public const CC_BYE       = 0x4A;
    public const CC_GRAB_FILE = 0x4B;
    public const CC_GRAB_DONE = 0x4C;
    public const CC_STAT      = 0x4D;
    public const CC_RENAME    = 0x4E;
    public const CC_CH_PASSW  = 0x4F;
    public const CC_LIMIT     = 0x80;
    public const CC_TEST      = 0x81;

    # RDIRENT Type Codes - directory blocks
    public const RDTYPE_END    = 0x00;
    public const RDTYPE_FILE   = 0x01;
    public const RDTYPE_DIR    = 0x02;
    public const RDTYPE_LINK   = 0x03;
    public const RDTYPE_SKIP   = 0x2a;

    protected function loadQuery(): RemoteRequest\Protocols\Dummy\Query
    {
        return new Fsp\Query();
    }

    protected function loadAnswer(): RemoteRequest\Protocols\Dummy\Answer
    {
        return new Fsp\Answer($this->rrLang);
    }
}
