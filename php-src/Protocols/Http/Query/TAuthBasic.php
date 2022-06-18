<?php

namespace kalanis\RemoteRequest\Protocols\Http\Query;


/**
 * Trait TAuthBasic
 * @package kalanis\RemoteRequest\Protocols\Http\Query
 * Authorization header trait
 */
trait TAuthBasic
{
    use TAuth;

    /** @var string */
    protected $username = '';
    /** @var string */
    protected $password = '';

    public function setCredentials(string $username, string $password = ''): void
    {
        $this->username = $username;
        $this->password = $password;
    }

    protected function authType(): string
    {
        return 'Basic';
    }

    protected function authKey(): string
    {
        return base64_encode(sprintf('%s:%s',
            strtr($this->username, [':' => '']),
            $this->password
        ));
    }
}
