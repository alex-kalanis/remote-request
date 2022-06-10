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
    /** @var bool */
    protected $inline = false;
    /** @var string */
    protected $userAgent = 'php-agent/1.2';
    /** @var string[] */
    protected $headers = [];
    /** @var resource */
    protected $contentStream = null;
    /** @var int */
    protected $contentLength = 0;
    /** @var string|null */
    protected $boundary = null;

    public function __construct()
    {
        $this->addHeader('Host', 'dummy.example');
        $this->addHeader('Accept', '*/*');
        $this->addHeader('User-Agent', $this->userAgent);
        $this->addHeader('Connection', 'close');
    }

    public function setMethod(string $method): self
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

    public function setInline(bool $inline)
    {
        $this->inline = $inline;
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

    public function setRequestSettings(Interfaces\ITarget $request): self
    {
        $this->host = $request->getHost();
        $this->port = $request->getPort();
        return $this;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;
        return $this;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Is current query set as inline?
     * @return bool
     */
    public function isInline(): bool
    {
        return $this->inline;
    }

    /**
     * Add HTTP header
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function addHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    /**
     * Remove HTTP header
     * @param string $name
     * @return $this
     */
    public function removeHeader(string $name): self
    {
        unset($this->headers[$name]);
        return $this;
    }

    /**
     * Add HTTP variables
     * @param string[] $array
     * @return $this
     */
    public function addValues($array): self
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
    public function addValue(string $key, $value): self
    {
        $this->content[$key] = ($value instanceof Query\Value) ? $value : new Query\Value((string)$value);
        return $this;
    }

    /**
     * Remove HTTP header
     * @param string $key
     * @return $this
     */
    public function removeValue(string $key): self
    {
        unset($this->content[$key]);
        return $this;
    }

    public function getData()
    {
        $this->contentStream = Protocols\Helper::getTempStorage();
        $this->contentLength = 0;
        $this->addHeader('Host', $this->getHostAndPort());

        $this->checkForMethod();
        $this->checkForFiles();
        $this->prepareBoundary();
        $this->prepareQuery();

        $this->contentLengthHeader();
        $this->contentTypeHeader();

        $storage = Protocols\Helper::getTempStorage();
        fwrite($storage, $this->renderRequestHeader());
        rewind($this->contentStream);
        stream_copy_to_stream($this->contentStream, $storage);
        rewind($storage);
        return $storage;
    }

    protected function getHostAndPort(): string
    {
        $portPart = '';
        if (is_int($this->port) && (80 != $this->port)) {
            $portPart .= ':' . $this->port;
        }
        return $this->host . $portPart;
    }

    protected function checkForMethod(): self
    {
        if (in_array($this->getMethod(), $this->multipartMethods)) {
            $this->inline = false;
        }
        return $this;
    }

    protected function checkForFiles(): self
    {
        if (!empty(array_filter($this->content, [$this, 'fileAnywhere']))) { // for files
            $this->inline = false;
        }
        return $this;
    }

    protected function prepareBoundary(): self
    {
        $this->boundary = $this->isMultipart() ? $this->generateBoundary() : null ;
        return $this;
    }

    protected function generateBoundary(): string
    {
        return 'PHPFsock------------------' . $this->generateRandomString();
    }

    protected function prepareQuery(): self
    {
        if (!$this->isInline()) {
            if ($this->isMultipart()) {
                $this->createMultipartRequest();
            } else {
                $this->contentLength += (int)fwrite($this->contentStream, $this->getSimpleRequest());
            }
        }
        return $this;
    }

    /**
     * Is current query set as multipart? (Know how to work with content values?)
     * @return bool
     */
    public function isMultipart(): bool
    {
        return !empty(array_filter($this->content, [$this, 'fileAnywhere']));
    }

    public function fileAnywhere($variable): bool
    {
        return is_object($variable) && ($variable instanceof Query\File);
    }

    protected function contentLengthHeader(): self
    {
        if (empty($this->contentLength)) {
            $this->removeHeader('Content-Length');
        } else {
            $this->addHeader('Content-Length', (int)$this->contentLength);
        }
        return $this;
    }

    protected function contentTypeHeader(): self
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

    protected function createMultipartRequest(): string
    {
        foreach ($this->content as $key => $value) {
            $this->contentLength += (int)fwrite($this->contentStream, '--' . $this->boundary . Http::DELIMITER);
            if ($value instanceof Query\File) {
                $filename = empty($value->getFilename()) ? '' : '; filename="' . urlencode($value->getFilename()) . '"';
                $this->contentLength += (int)fwrite($this->contentStream, 'Content-Disposition: form-data; name="' . urlencode($key) . '"' . $filename . Http::DELIMITER);
                $this->contentLength += (int)fwrite($this->contentStream, 'Content-Type: ' . $value->getMimeType() . Http::DELIMITER . Http::DELIMITER);
                $source = $value->getStream();
                rewind($source);
                $this->contentLength += (int)stream_copy_to_stream($source, $this->contentStream);
                $this->contentLength += (int)fwrite($this->contentStream, Http::DELIMITER);
            } else {
                $this->contentLength += (int)fwrite($this->contentStream, 'Content-Disposition: form-data; name="' . urlencode($key) . '"' . Http::DELIMITER . Http::DELIMITER);
                $this->contentLength += (int)fwrite($this->contentStream, $value->getContent() . Http::DELIMITER);
            }
        }
        $this->contentLength += (int)fwrite($this->contentStream, '--' . $this->boundary . '--' . Http::DELIMITER);
        return '';
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
