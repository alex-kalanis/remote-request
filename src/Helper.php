<?php

namespace RemoteRequest;

use RemoteRequest\Connection;
use RemoteRequest\Schemas;
use RemoteRequest\Sockets;

/**
 * Simplified reading from remote machine with a whole power of RemoteRequest and mainly streams underneath
 * -> throw Curl into /dev/null
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
        'sequence' => 0,
        'secret' => 0,
        'seek' => 0,
    ];
    protected $contextParams = [];

    /**
     * @param string $link link to remote source (server, page, ...)
     * @param string|string[] $postContent array(key=>value) for http or fsp, string otherwise
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
        $libSchema = $this->getLibSchema($schema, $parsedLink);
        $libQuery = $this->getLibRequest($schema, $parsedLink, $libSchema);
        return $this->getLibResponseProcessor($schema)->setResponse(
            $this->getLibConnection($libQuery)
                ->setProtocolSchema($libSchema)
                ->setData($libQuery)
                ->getResponse()
        );
    }

    protected function getLibConnection(Protocols\Dummy\Query $libQuery): Connection\Processor
    {
        return new Connection\Processor($this->getLibSockets($libQuery));
    }

    protected function getLibSockets(Protocols\Dummy\Query $libQuery): ?Sockets\ASocket
    {
        if (!empty($this->contextParams)) {
            $processing = new Sockets\Stream();
            return $processing->setContextOptions($this->contextParams);
        } elseif ($this->connectionParams['permanent']) {
            return new Sockets\Pfsocket();
        } elseif ($libQuery instanceof Protocols\Fsp\Query) {
            return new Sockets\Socket();
        } else {
            return new Sockets\Fsocket();
        }
    }

    /**
     * @param string $schema
     * @param array $parsedLink
     * @return Schemas\ASchema
     * @throws RequestException
     */
    protected function getLibSchema(string $schema, $parsedLink): Schemas\ASchema
    {
        $libWrapper = $this->getSchema($schema);
        return $libWrapper->setTarget(
            $parsedLink["host"],
            empty($parsedLink["port"]) ? $libWrapper->getPort() : $parsedLink["port"],
            empty($this->connectionParams['timeout']) ? null : (int)$this->connectionParams['timeout']
        );
    }

    /**
     * @param string $schema
     * @return Schemas\ASchema
     * @throws RequestException
     */
    protected function getSchema(string $schema): Schemas\ASchema
    {
        switch ($schema) {
            case 'tcp':
                return new Schemas\Tcp();
            case 'udp':
            case 'fsp':
                return new Schemas\Udp();
            case 'http':
                return new Schemas\Tcp();
            case 'https':
                return new Schemas\Ssl();
            case 'file':
                return new Schemas\File();
            default:
                throw new RequestException('Unknown protocol schema for known schema ' . $schema);
        }
    }

    /**
     * @param string $schema
     * @param array $parsed from parse_url()
     * @param Connection\ITarget $settings
     * @return Protocols\Dummy\Query
     * @throws RequestException
     */
    protected function getLibRequest(string $schema, array $parsed, Connection\ITarget $settings): Protocols\Dummy\Query
    {
        switch ($schema) {
            case 'tcp':
            case 'udp':
            case 'file':
                $query = new Protocols\Dummy\Query();
                $query->maxLength = $this->connectionParams['maxLength'];
                $query->body = $this->postContent;
                return $query;
            case 'fsp':
                $query = new Protocols\Fsp\Query();
                return $query
                    ->setCommand((int)$this->connectionParams['method'])
                    ->setSequence((int)$this->connectionParams['sequence'])
                    ->setKey((int)$this->connectionParams['secret'])
                    ->setFilePosition((int)$this->connectionParams['seek'])
                    ->setData($this->postContent)
                ;
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
            case 'fsp':
                return new Protocols\Fsp\Answer();
            case 'http':
            case 'https':
                return new Protocols\Http\Answer();
            default:
                throw new RequestException('Unknown response available for schema ' . $schema);
        }
    }
}