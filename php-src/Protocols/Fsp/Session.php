<?php

namespace kalanis\RemoteRequest\Protocols\Fsp;


use kalanis\RemoteRequest\Interfaces\IRRTranslations;
use kalanis\RemoteRequest\RequestException;
use kalanis\RemoteRequest\Traits\TLang;


/**
 * Class Session
 * @package kalanis\RemoteRequest\Protocols\Fsp
 * Session in FSP
 */
class Session
{
    use TLang;

    /** @var string|null */
    protected $host = null;
    /** @var int[] */
    protected static $key = null;
    /** @var array<string, array<int, Session\Sequence>> */
    protected static $sequence = [];

    public function __construct(?IRRTranslations $lang = null)
    {
        $this->setRRLang($lang);
    }

    public function setHost(string $host): self
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
     * @throws RequestException
     * @return int
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
     * @throws RequestException
     * @return Session
     */
    public function setKey(int $key): self
    {
        $this->checkHost();
        static::$key[$this->host] = $key;
        return $this;
    }

    /**
     * @throws RequestException
     * @return int
     */
    public function getSequence(): int
    {
        return $this->generateSequence()->getKey();
    }

    /**
     * @param int $sequence
     * @throws RequestException
     * @return $this
     */
    public function updateSequence(int $sequence): self
    {
        $this->getLastSequence()->checkSequence($sequence)->updateSequence();
        return $this;
    }

    /**
     * @throws RequestException
     * @return Session\Sequence
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
        return Session\Sequence::newSequence($this->getRRLang());
    }

    /**
     * @throws RequestException
     * @return Session\Sequence
     */
    protected function getLastSequence(): Session\Sequence
    {
        $this->checkHost();
        $last = end(static::$sequence[$this->host]);
        if (false === $last) {
            throw new RequestException($this->getRRLang()->rrFspEmptySequence());
        }
        return $last;
    }

    /**
     * @throws RequestException
     * @return $this
     */
    protected function checkHost(): self
    {
        if (empty($this->host)) {
            throw new RequestException($this->getRRLang()->rrFspEmptyHost());
        }
        return $this;
    }
}
