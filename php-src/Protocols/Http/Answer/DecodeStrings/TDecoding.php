<?php

namespace kalanis\RemoteRequest\Protocols\Http\Answer\DecodeStrings;


/**
 * Trait TDecoding
 * @package kalanis\RemoteRequest\Protocols\Http\Answer\DecodeStrings
 */
trait TDecoding
{
    /** @var ADecoder[] */
    protected $stringDecoders = [];

    public function addStringDecoding(ADecoder $decoder): self
    {
        $this->stringDecoders[] = $decoder;
        return $this;
    }

    public function processStringDecode(string $content): string
    {
        foreach ($this->stringDecoders as $decoder) {
            $header = $this->getStringHeader($decoder->getHeaderKey());
            if (!empty($header) && $decoder->canDecode(strval($header))) {
                $content = $decoder->processDecode($content);
            }
        }
        return $content;
    }

    protected function getStringHeader(string $key, ?string $default = null): ?string
    {
        $headers = $this->getAllHeaders();
        return isset($headers[$key]) ? strval(reset($headers[$key])) : $default;
    }

    /**
     * @return array<string, array<string>>
     */
    abstract public function getAllHeaders(): array;
}
