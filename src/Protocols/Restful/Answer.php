<?php

namespace RemoteRequest\Protocols\Restful;


use RemoteRequest\Protocols;


/**
 * Class Answer
 * @package RemoteRequest\Protocols\Restful
 * Process server's answer - REST API
 */
class Answer extends Protocols\Http\Answer
{
    public function getDecodedContent(bool $asArray = false)
    {
        return json_decode(trim($this->getContent()), $asArray);
    }
}
