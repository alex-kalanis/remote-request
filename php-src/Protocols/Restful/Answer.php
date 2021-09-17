<?php

namespace kalanis\RemoteRequest\Protocols\Restful;


use kalanis\RemoteRequest\Protocols;


/**
 * Class Answer
 * @package kalanis\RemoteRequest\Protocols\Restful
 * Process server's answer - REST API
 */
class Answer extends Protocols\Http\Answer
{
    public function getDecodedContent(bool $asArray = false)
    {
        return json_decode(trim($this->getContent()), $asArray);
    }
}
