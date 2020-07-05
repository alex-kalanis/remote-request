<?php

namespace RemoteRequest\Protocols\Fsp\Session;

use RemoteRequest\RequestException;

/**
 * Session sequences in FSP
 */
class Sequence
{
    protected $key = 0;
    protected $created = 0.0;
    protected $done = 0.0;
    protected $length = 0.0;

    public static function newSequence(): self
    {
        $lib = new static();
        return $lib->generateSequence();
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
        return rand(0, 255);
    }

    public function getKey(): string
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
            throw new RequestException(sprintf('Wrong sequence! Got %d want %d', $sequence, $this->key));
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
