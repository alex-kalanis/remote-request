<?php

namespace kalanis\RemoteRequest\Protocols\Fsp\Session;


use kalanis\RemoteRequest\Interfaces\IRRTranslations;
use kalanis\RemoteRequest\RequestException;


/**
 * Class Sequence
 * @package kalanis\RemoteRequest\Protocols\Fsp\Session
 * Session sequences in FSP
 */
class Sequence
{
    protected $lang = null;
    protected $key = 0;
    protected $created = 0.0;
    protected $done = 0.0;
    protected $length = 0.0;

    public static function newSequence(IRRTranslations $lang): self
    {
        $lib = new static($lang);
        return $lib->generateSequence();
    }

    public function __construct(IRRTranslations $lang)
    {
        $this->lang = $lang;
    }

    public function generateSequence(): self
    {
        $this->key = $this->getRandInitial();
        $this->created = microtime(true);
        return $this;
    }

    /**
     * @return int
     * @codeCoverageIgnore because how to process random number?
     */
    protected function getRandInitial(): int
    {
        return rand(0, 65535);
    }

    public function getKey(): int
    {
        return $this->key;
    }

    /**
     * @param int $sequence
     * @return $this
     * @throws RequestException
     */
    public function checkSequence(int $sequence): self
    {
        if ($this->key != $sequence) {
            throw new RequestException($this->lang->rrFspWrongSequence($sequence, $this->key));
        }
        return $this;
    }

    public function updateSequence(): self
    {
        $this->done = microtime(true);
        $this->length = $this->done - $this->created;
        return $this;
    }
}
