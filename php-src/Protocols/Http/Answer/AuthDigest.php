<?php

namespace kalanis\RemoteRequest\Protocols\Http\Answer;


use kalanis\RemoteRequest\Protocols\Http;


/**
 * Class AuthDigest
 * @package kalanis\RemoteRequest\Protocols\Http\Answer
 * Message from the remote server compilation - protocol http - parse digest info
 *
 * How it works:
 * - ask for data without auth
 * - server returns 401 with WWW-Authenticate header
 * - pass that data to this class to parse header
 * - fill auth query with data from this class and original query
 * - ask for data again
 */
class AuthDigest extends Http\Answer
{
    protected string $authType = '';
    /** @var string[] */
    protected array $authHeader = [];

    public function processContent(): self
    {
        if (401 == $this->getCode()) {
            // unauth
            $headerData = strval($this->getHeader('WWW-Authenticate'));
            preg_match('#^\s?([^\s]+)\s#i', $headerData, $types);
            preg_match_all('#([^\s]+)="([^"]+)"#i', $headerData, $matches);
            $this->authType = $types[1] ?: 'Basic';
            foreach ($matches[0] as $key => $info) {
                $this->authHeader[$matches[1][$key]] = $matches[2][$key];
            }
        }
        return $this;
    }

    public function getAuthType(): string
    {
        return $this->authType;
    }

    public function getAuthRealm(): ?string
    {
        return $this->getAuthHeader('realm');
    }

    /**
     * @return string[]
     */
    public function getQualitiesOfProtection(): array
    {
        return explode(',', strval($this->getAuthHeader('qop')));
    }

    public function getRemoteRandomNumber(): ?string
    {
        return $this->getAuthHeader('nonce');
    }

    public function getDataToReturn(): ?string
    {
        return $this->getAuthHeader('opaque');
    }

    public function getAlgorithm(): string
    {
        return $this->getAuthHeader('algorithm') ?: 'md5';
    }

    protected function getAuthHeader(string $key): ?string
    {
        return $this->authHeader[$key] ?? null;
    }
}
