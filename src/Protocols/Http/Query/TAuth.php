<?php

namespace RemoteRequest\Protocols\Http\Query;


/**
 * Trait TAuth
 * @package RemoteRequest\Protocols\Http\Query
 * Authorization header trait
 */
trait TAuth
{
    public function authHeader(): void
    {
        $this->addHeader('Authorization', sprintf('%s %s', $this->authType(), $this->authKey()));
    }

    abstract protected function authType(): string;

    abstract protected function authKey(): string;

    abstract public function addHeader(string $name, string $value);
}
