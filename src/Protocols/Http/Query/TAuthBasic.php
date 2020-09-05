<?php

namespace RemoteRequest\Protocols\Http\Query;

/**
 * Authorization header trait
 */
trait TAuthBasic
{
    use TAuth;

    protected $username = '';
    protected $password = '';

    public function setCredentials(string $username, string $password = '')
    {
        $this->username = $username;
        $this->password = $password;
        return $this;
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