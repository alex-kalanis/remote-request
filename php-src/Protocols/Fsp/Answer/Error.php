<?php

namespace kalanis\RemoteRequest\Protocols\Fsp\Answer;


use kalanis\RemoteRequest\Protocols\Fsp;
use kalanis\RemoteRequest\RequestException;


/**
 * Class Error
 * @package kalanis\RemoteRequest\Protocols\Fsp\Answer
 * Process default answer aka what did you send?!
 */
class Error extends AAnswer
{
    protected int $errorCode = 0;
    protected string $errorMessage = '';
    protected bool $hardWay = false;

    public function setHardWay(bool $hardWay): self
    {
        $this->hardWay = $hardWay;
        return $this;
    }

    public function process(): parent
    {
        $this->errorMessage = substr($this->answer->getContent(), 0, -1);
        $extra = $this->answer->getExtraData();
        if (!empty($extra)) {
            $this->errorCode = Fsp\Strings::mb_ord($extra);
        }
        return $this;
    }

    /**
     * @throws RequestException
     * @return RequestException
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
