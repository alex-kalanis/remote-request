<?php

namespace kalanis\RemoteRequest\Protocols\Http\Answer\DecodeStreams;


/**
 * Trait TDecoding
 * @package kalanis\RemoteRequest\Protocols\Http\Answer\DecodeStreams
 */
trait TDecoding
{
    /** @var ADecoder[] */
    protected $streamsDecoders = [];

    public function addStreamDecoder(ADecoder $decoder): self
    {
        $this->streamsDecoders[] = $decoder;
        return $this;
    }

    /**
     * @param resource $content stream handler
     * @return resource
     */
    public function processStreamDecode($content)
    {
        foreach ($this->streamsDecoders as $decoder) {
            $header = $this->getStreamHeader($decoder->getHeaderKey());
            if (!empty($header) && $decoder->canDecode(strval($header))) {
                $content = $decoder->processDecode($content);
            }
        }
        return $content;
    }

    protected function getStreamHeader($key, $default = null): ?string
    {
        $headers = $this->getAllHeaders();
        return isset($headers[$key])? (string)reset($headers[$key]) : $default;
    }

    abstract public function getAllHeaders(): array;
}
