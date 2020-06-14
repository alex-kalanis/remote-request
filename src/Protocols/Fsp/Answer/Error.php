<?php

namespace RemoteRequest\Protocols\Fsp\Answer;

use RemoteRequest\Protocols\Fsp;
use RemoteRequest\RequestException;

/**
 * Process default answer aka what did you send?!
 */
class Error extends AAnswer
{
    protected $errorCode = 0;
    protected $errorMessage = '';
    protected $hardWay = false;

    public function setHardWay(bool $hardWay): self
    {
        $this->hardWay = $hardWay;
        return $this;
    }

    public function process(): parent
    {
        $this->errorMessage = $this->answer->getContent();
        $extra = $this->answer->getExtraData();
        if (!empty($extra)) {
            $this->errorCode = Fsp\Strings::mb_ord($extra);
        }
        return $this;
    }

    /**
     * @return RequestException
     * @throws RequestException
     */
    public function getError(): RequestException
    {
        $ex = new RequestException($this->errorMessage, $this->errorCode);
        if ($this->hardWay) {
            throw $ex;
        }
        return $ex;
    }
}