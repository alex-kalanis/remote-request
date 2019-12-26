<?php

namespace RemoteRequest;

use RemoteRequest\Connection;
use RemoteRequest\Wrappers;

/**
 * Zjednodusene nacitani ze vzdaleneho stroje pomoci cele sily RemoteRequest a hlavne streamu dole
 * aneb cele prcani s curl patri do /dev/null
 */
class Helper
{
    protected $link = ''; // target
    protected $postContent = ''; // what to say to the target
    protected $connectionParams = [
        'timeout' => 30,
        'maxLength' => 0,
        'method' => 'get',
        'multipart' => false,
        'permanent' => false,
    ];
    protected $contextParams = [];

    /**
     * @param string $link link to remote source (server, page, ...)
     * @param string|string[] $postContent array(key=>value) for http, string otherwise
     * @param array $connectionParams overwrite default values for connection
     * @param array $contextParams added to stream context (like skipping ssl checkup)
     * @return string
     */
    public static function getRemoteContent(string $link, $postContent = '', array $connectionParams = [], array $contextParams = []): string
    {
        $lib = new static();
        $lib
            ->setLink($link)
            ->setPostContent($postContent)
            ->setConnectionParams($connectionParams)
            ->setContextParams($contextParams)
        ;
        return $lib->getResponse()->getContent();
    }

    public function setLink(string $link)
    {
        $this->link = $link;
        return $this;
    }

    public function setPostContent($postContent = '')
    {
        $this->postContent = $postContent;
        return $this;
    }

    public function setConnectionParams(array $params = [])
    {
        $this->connectionParams = array_merge($this->connectionParams, $params);
        return $this;
    }

    public function setContextParams(array $params = [])
    {
        $this->contextParams = $params;
        return $this;
    }

    /**
     * @return Protocols\Dummy\Answer
     * @throws RequestException
     */
    public function getResponse(): Protocols\Dummy\Answer
    {
        $parsedLink = parse_url($this->link);
        $schema = strtolower($parsedLink["scheme"]);
        $libWrapper = $this->getLibWrapper($schema, $parsedLink);
        $libQuery = $this->getLibRequest($schema, $parsedLink, $libWrapper);
        return $this->getLibResponseProcessor($schema)->setResponse(
            $this->getLibConnection()
                ->setProtocolWrapper($libWrapper)
                ->setData($libQuery)
                ->getResponse()
        );
    }

    protected function getLibConnection(): Connection\Processor
    {
        return new Connection\Processor($this->getLibPointers());
    }

    protected function getLibPointers(): Pointers\APointer
    {
        if (!empty($this->contextParams)) {
            $processing = new Pointers\Stream();
            return $processing->setContextOptions($this->contextParams);
        } elseif ($this->connectionParams['permanent']) {
            return new Pointers\Pfsocket();
        } else {
            return new Pointers\Fsocket();
        }
    }

    /**
     * @param string $schema
     * @param array $parsedLink
     * @return Wrappers\AWrapper
     * @throws RequestException
     */
    protected function getLibWrapper(string $schema, $parsedLink): Wrappers\AWrapper
    {
        $libWrapper = $this->getWrapper($schema);
        return $libWrapper->setTarget(
            $parsedLink["host"],
            empty($parsedLink["port"]) ? $libWrapper->getPort() : $parsedLink["port"],
            empty($this->connectionParams['timeout']) ? null : (int)$this->connectionParams['timeout']
        );
    }

    /**
     * @param string $schema
     * @return Wrappers\AWrapper
     * @throws RequestException
     */
    protected function getWrapper(string $schema): Wrappers\AWrapper
    {
        switch ($schema) {
            case 'tcp':
                return new Wrappers\Tcp();
            case 'udp':
                return new Wrappers\Udp();
            case 'http':
                return new Wrappers\Tcp();
            case 'https':
                return new Wrappers\Ssl();
            case 'file':
                return new Wrappers\File();
            default:
                throw new RequestException('Unknown protocol wrapper for schema ' . $schema);
        }
    }

    /**
     * @param string $schema
     * @param array $parsed from parse_url()
     * @param Connection\ISettings $settings
     * @return Protocols\Dummy\Query
     * @throws RequestException
     */
    protected function getLibRequest(string $schema, array $parsed, Connection\ISettings $settings): Protocols\Dummy\Query
    {
        switch ($schema) {
            case 'tcp':
            case 'udp':
            case 'file':
                $query = new Protocols\Dummy\Query();
                $query->maxLength = $this->connectionParams['maxLength'];
                $query->body = $this->postContent;
                return $query;
            case 'http':
            case 'https':
                $query = new Protocols\Http\Query();
                $query->maxLength = $this->connectionParams['maxLength'];
                return $query
                    ->setRequestSettings($settings)
                    ->setPath($parsed["path"] . (!empty($parsed["query"]) ? '?' . $parsed["query"] : '' ))
                    ->setMethod($this->getMethod())
                    ->setMultipart($this->connectionParams['multipart'])
                    ->addValues(empty($this->postContent) ? [] : (array)$this->postContent)
                ;
            default:
                throw new RequestException('Unknown request available for schema ' . $schema);
        }
    }

    protected function getMethod(): string
    {
        $method = strtoupper($this->connectionParams['method']);
        return (in_array($method, ['GET', 'POST', 'PUT', 'DELETE'])) ? $method : 'GET' ;
    }

    /**
     * @param string $schema
     * @return Protocols\Dummy\Answer
     * @throws RequestException
     */
    protected function getLibResponseProcessor(string $schema): Protocols\Dummy\Answer
    {
        switch ($schema) {
            case 'tcp':
            case 'udp':
            case 'file':
                return new Protocols\Dummy\Answer();
            case 'http':
            case 'https':
                return new Protocols\Http\Answer();
            default:
                throw new RequestException('Unknown response available for schema ' . $schema);
        }
    }
}