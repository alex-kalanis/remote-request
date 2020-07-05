<?php

namespace RemoteRequest\Protocols\Fsp;

use RemoteRequest\RequestException;

/**
 * Session in FSP
 */
class Session
{
    /** @var string|null */
    protected $host = null;
    /** @var string[] */
    protected static $key = null;
    /** @var Session\Sequence[] */
    protected static $sequence = [];

    public function setHost(?string $host): self
    {
        $this->host = $host;
        static::$sequence[$this->host] = [];
        return $this;
    }

    public function clear(): self
    {
        if (empty($this->host)) {
            static::$key = [];
            static::$sequence = [];
        } else {
            unset(static::$key[$this->host]);
            unset(static::$sequence[$this->host]);
            $this->host = null;
        }
        return $this;
    }

    public function hasKey(): bool
    {
        return (empty($this->host))? false : (!empty(static::$key[$this->host]));
    }

    /**
     * @return int
     * @throws RequestException
     */
    public function getKey(): int
    {
        $this->checkHost();
        return $this->hasKey() ? static::$key[$this->host] : $this->getRandInitial();
    }

    /**
     * @return int
     * @codeCoverageIgnore because how to process random number?
     */
    protected function getRandInitial(): int
    {
        return rand(0, 255);
    }

    /**
     * @param int $key
     * @return Session
     * @throws RequestException
     */
    public function setKey(int $key): self
    {
        $this->checkHost();
        static::$key[$this->host] = $key;
        return $this;
    }

    /**
     * @return int
     * @throws RequestException
     */
    public function getSequence(): int
    {
        return $this->generateSequence()->getKey();
    }

    /**
     * @param int $sequence
     * @return $this
     * @throws RequestException
     */
    public function updateSequence(int $sequence): self
    {
        $this->getLastSequence()->checkSequence($sequence)->updateSequence();
        return $this;
    }

    /**
     * @return Session\Sequence
     * @throws RequestException
     */
    protected function generateSequence(): Session\Sequence
    {
        $this->checkHost();
        $sequence = $this->sequencer();
        static::$sequence[$this->host][] = $sequence;
        return $sequence;
    }

    /**
     * @return Session\Sequence
     * @codeCoverageIgnore it contains generator!
     */
    protected function sequencer(): Session\Sequence
    {
        return Session\Sequence::newSequence();
    }

    /**
     * @return Session\Sequence
     * @throws RequestException
     */
    protected function getLastSequence(): Session\Sequence
    {
        $this->checkHost();
        $last = end(static::$sequence[$this->host]);
        if (false === $last) {
            throw new RequestException('Empty sequence!');
        }
        return $last;
    }

    /**
     * @return $this
     * @throws RequestException
     */
    protected function checkHost(): self
    {
        if (empty($this->host)) {
            throw new RequestException('Empty host!');
        }
        return $this;
    }
}
