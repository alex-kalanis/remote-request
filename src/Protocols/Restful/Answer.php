<?php

namespace RemoteRequest\Protocols\Restful;

use RemoteRequest\Protocols;

/**
 * Process server's answer - REST API
 */
class Answer extends Protocols\Http\Answer
{
    public function getDecodedContent(bool $asArray = false)
    {
        return json_decode(trim($this->getContent()), $asArray);
    }
}