<?php

namespace kalanis\RemoteRequest\Protocols\Http;


use kalanis\RemoteRequest\Interfaces;
use kalanis\RemoteRequest\Protocols;
use kalanis\RemoteRequest\Protocols\Http;


/**
 * Class Query
 * @package kalanis\RemoteRequest\Protocols\Http
 * Message to the remote server compilation - protocol http
 */
class Query extends Protocols\Dummy\Query implements Interfaces\ITarget
{
    /** @var string */
    protected $host = '';
    /** @var int */
    protected $port = 80;
    /** @var string */
    protected $path = '';
    /** @var string */
    protected $method = 'POST';
    /** @var string[] */
    protected $availableMethods = ['GET', 'POST', 'PUT', 'DELETE'];
    /** @var string[] */
    protected $multipartMethods = ['POST', 'PUT'];
    /** @var Query\Value[] */
    protected $content = [];
    /** @var bool|null */
    protected $multipart = false;
    /** @var string */
    protected $userAgent = 'php-agent/1.2';
    /** @var string[] */
    protected $headers = [];
    /** @var string */
    protected $contentQuery = '';
    /** @var int|null */
    protected $contentLength = null;
    /** @var string|null */
    protected $boundary = null;

    public function __construct()
    {
        $this->addHeader('Host', 'dummy.example');
        $this->addHeader('Accept', '*/*');
        $this->addHeader('User-Agent', $this->userAgent);
        $this->addHeader('Connection', 'close');
    }

    public function setMethod(string $method)
    {
        $method = strtoupper($method);
        if (in_array($method, $this->availableMethods)) {
            $this->method = $method;
        }
        return $this;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function setMultipart(?bool $multipart)
    {
        $this->multipart = $multipart;
        return $this;
    }

    public function setHost(string $host)
    {
        $this->host = $host;
        return $this;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function setPort(int $port)
    {
        $this->port = $port;
        return $this;
    }

    public function getPort(): ?int
    {
        return $this->port;
    }

    public function setRequestSettings(Interfaces\ITarget $request)
    {
        $this->host = $request->getHost();
        $this->port = $request->getPort();
        return $this;
    }

    public function setPath(string $path)
    {
        $this->path = $path;
        return $this;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Is current query set as inline? (Has no multipart definition?)
     * @return bool
     */
    public function isInline(): bool
    {
        return is_null($this->multipart);
    }

    /**
     * Is current query set as multipart? (Know how to work with content values?)
     * @return bool
     */
    public function isMultipart(): bool
    {
        if (is_bool($this->multipart)) {
            return $this->multipart;
        }
        return false;
    }

    /**
     * Add HTTP header
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function addHeader(string $name, string $value)
    {
        $this->headers[$name] = $value;
        return $this;
    }

    /**
     * Remove HTTP header
     * @param string $name
     * @return $this
     */
    public function removeHeader(string $name)
    {
        unset($this->headers[$name]);
        return $this;
    }

    /**
     * Add HTTP variables
     * @param string[] $array
     * @return $this
     */
    public function addValues($array)
    {
        array_walk($array, function ($value, $key) {
            $this->addValue($key, $value);
        });
        return $this;
    }

    /**
     * Add HTTP variable
     * @param string $key
     * @param string|Query\Value $value
     * @return $this
     */
    public function addValue(string $key, $value)
    {
        $this->content[$key] = ($value instanceof Query\Value) ? $value : new Query\Value((string)$value);
        return $this;
    }

    /**
     * Remove HTTP header
     * @param string $key
     * @return $this
     */
    public function removeValue(string $key)
    {
        unset($this->content[$key]);
        return $this;
    }

    public function getData(): string
    {
        $this->addHeader('Host', $this->getHostAndPort());

        $this->checkForMethod();
        $this->checkForFiles();
        $this->prepareBoundary();
        $this->prepareQuery();

        $this->contentLengthHeader();
        $this->contentTypeHeader();

        return sprintf("%s%s", $this->renderRequestHeader(), $this->contentQuery);
    }

    protected function getHostAndPort(): string
    {
        $portPart = '';
        if (is_int($this->port) && (80 != $this->port)) {
            $portPart .= ':' . $this->port;
        }
        return $this->host . $portPart;
    }

    protected function checkForMethod()
    {
        if (in_array($this->getMethod(), $this->multipartMethods) && is_null($this->multipart)) {
            $this->multipart = false;
        }
        return $this;
    }

    protected function checkForFiles()
    {
        if ((bool)count(array_filter($this->content, function ($content) {
            return $content instanceof Query\File;
        }))) { // for files
            $this->multipart = true;
        }
        return $this;
    }

    protected function prepareBoundary()
    {
        $this->boundary = $this->isMultipart() ? $this->generateBoundary() : null ;
        return $this;
    }

    protected function generateBoundary(): string
    {
        return 'PHPFsock------------------' . $this->generateRandomString();
    }

    protected function prepareQuery()
    {
        $this->contentQuery = !$this->isInline() ? ($this->isMultipart() ? $this->getMultipartRequest() : $this->getSimpleRequest()) : '';
        $this->contentLength = !$this->isInline() ? mb_strlen($this->contentQuery) : null ;
        return $this;
    }

    protected function contentLengthHeader()
    {
        if (is_null($this->contentLength)) {
            $this->removeHeader('Content-Length');
        } else {
            $this->addHeader('Content-Length', $this->contentLength);
        }
        return $this;
    }

    protected function contentTypeHeader()
    {
        if (in_array($this->getMethod(), $this->multipartMethods)) {
            if (is_null($this->boundary)) {
                $this->addHeader('Content-Type', 'application/x-www-form-urlencoded');
            } else {
                $this->addHeader('Content-Type', 'multipart/form-data; boundary=' . $this->boundary);
            }
        } else {
            $this->removeHeader('Content-Type');
        }
        return $this;
    }

    /**
     * Generate and returns random string with combination of numbers and chars with specified length
     * @param int $stringLength
     * @return string
     */
    protected function generateRandomString(int $stringLength = 16): string
    {
        $all = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z"];
        $string = "";
        for ($i = 0; $i < $stringLength; $i++) {
            $rand = mt_rand(0, count($all) - 1);
            $string .= $all[$rand];
        }
        return $string;
    }

    protected function getMultipartRequest(): string
    {
        $tempContent = '';
        foreach ($this->content as $key => $value) {
            $tempContent .= '--' . $this->boundary . Http::DELIMITER;
            if ($value instanceof Query\File) {
                $filename = empty($value->getFilename()) ? '' : '; filename="' . urlencode($value->getFilename()) . '"';
                $tempContent .= 'Content-Disposition: form-data; name="' . urlencode($key) . '"' . $filename . Http::DELIMITER;
                $tempContent .= 'Content-Type: ' . $value->getMimeType() . Http::DELIMITER . Http::DELIMITER;
                $tempContent .= $value->getContent() . Http::DELIMITER;
            } else {
                $tempContent .= 'Content-Disposition: form-data; name="' . urlencode($key) . '"' . Http::DELIMITER . Http::DELIMITER;
                $tempContent .= $value->getContent() . Http::DELIMITER;
            }
        }
        $tempContent .= '--' . $this->boundary . '--' . Http::DELIMITER;
        return $tempContent;
    }

    /**
     * From defined headers make string as defined in RFC
     * @return string
     */
    protected function renderRequestHeader(): string
    {
        $header = $this->getQueryHeaders();
        return sprintf('%s%s%s',
            $this->getQueryTarget() . Http::DELIMITER,
            (mb_strlen($header) ? $header . Http::DELIMITER : ''),
            Http::DELIMITER
        );
    }

    /**
     * Get headers itself as string param to query
     * @return string
     */
    protected function getQueryHeaders(): string
    {
        return implode(Http::DELIMITER, array_map(function ($key, $value) {
            return sprintf('%s: %s', $key, $value);
        }, array_keys($this->headers), array_values($this->headers)));
    }

    /**
     * Top line, what server is a target
     * @return string
     */
    protected function getQueryTarget(): string
    {
        return sprintf('%1$s %2$s HTTP/1.1', $this->getMethod(), $this->getPathAndParams());
    }

    /**
     * Which path is target
     * @return string
     */
    protected function getPathAndParams(): string
    {
        $requestPart = '';
        if ($this->isInline() && !empty($this->content)) {
            $requestPart .= (false === mb_strpos($this->path, '?')) ? '?' : '&' ;
            $requestPart .= $this->getSimpleRequest();
        }
        return $this->path . $requestPart;
    }

    /**
     * Get pairs of content into encoded string
     * @return string
     */
    protected function getSimpleRequest(): string
    {
        return implode('&', array_map(function ($key, Http\Query\Value $value) {
            return sprintf('%s=%s', urlencode($key), urlencode($value->getContent()));
        }, array_keys($this->content), array_values($this->content)));
    }
}
