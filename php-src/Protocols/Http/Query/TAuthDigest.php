<?php

namespace kalanis\RemoteRequest\Protocols\Http\Query;


/**
 * Trait TAuthDigest
 * @package kalanis\RemoteRequest\Protocols\Http\Query
 * Authorization header trait
 * @link https://datatracker.ietf.org/doc/html/rfc2069
 * @link https://en.wikipedia.org/wiki/Digest_access_authentication
 */
trait TAuthDigest
{
    use TAuth;

    protected string $username = '';
    protected string $password = '';
    protected string $realm = '';
    protected string $remoteRandomNumber = '';
    protected string $localRandomNumber = '';
    protected string $returnToServer = '';
    protected string $qualityOfProtection = '';
    protected string $algorithm = 'md5';
    protected string $requestCounter = '';

    protected static int $queriesCounter = 1;

    public function setCredentials(string $username, string $password, string $realm): void
    {
        $this->username = $username;
        $this->password = $password;
        $this->realm = $realm;
    }

    public function setProperties(string $remoteRandomNumber, string $returnToServer, string $qualityOfProtection, string $algorithm = 'md5'): void
    {
        $this->localRandomNumber = $this->getRandomString();
        $this->remoteRandomNumber = $remoteRandomNumber;
        $this->returnToServer = $returnToServer;
        $this->qualityOfProtection = $qualityOfProtection;
        $this->algorithm = $algorithm;
        $this->requestCounter = sprintf('%08d', static::$queriesCounter);
        static::$queriesCounter++;
    }

    /**
     * @return string
     * @codeCoverageIgnore random generator
     */
    protected function getRandomString(): string
    {
        return substr(md5(strval(rand())), 0, 8);
    }

    public function authHeader(): void
    {
        $this->addHeader('Authorization',
            sprintf('%s username="%s", realm="%s", nonce="%s", uri="%s", qop="%s", nc="%s", cnonce="%s", response="%s", opaque="%s"',
                $this->authType(), $this->username, $this->realm, $this->remoteRandomNumber, $this->getPath(), $this->qualityOfProtection,
                $this->requestCounter, $this->localRandomNumber, $this->authKey(), $this->returnToServer));
    }

    protected function authType(): string
    {
        return 'Digest';
    }

    protected function authKey(): string
    {
        $A1 = $this->hash($this->username . ':' . $this->realm . ':' . $this->password);
        $A2 = $this->hash($this->getMethod() . ':' . $this->getPath());
//        return $this->hash($A1 . ':' . $this->remoteRandomNumber . ':' . $A2); // from rfc
        return $this->hash($A1 . ':' . $this->remoteRandomNumber . ':' . $this->requestCounter . ':' . $this->localRandomNumber . ':' . $this->qualityOfProtection . ':' . $A2); // from wiki
    }

    protected function hash(string $what): string
    {
        switch ($this->algorithm) {
            case 'md5':
            default:
                return md5($what);
        }
    }

    abstract public function getMethod(): string;

    abstract public function getPath(): string;
}
