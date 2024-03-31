<?php

namespace kalanis\RemoteRequest\Protocols\Http\Answer\DecodeStreams;


/**
 * Class ADecoder
 * @package kalanis\RemoteRequest\Protocols\Http\Answer\DecodeStreams
 * Decode content passed by streams
 * Due changes by content encoding there shall be a way to expand to the original content
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Encoding
 */
abstract class ADecoder
{
    /** @var string[] */
    protected array $contentEncoded = [];

    public function getHeaderKey(): string
    {
        return 'Content-Encoding';
    }

    public function canDecode(string $header): bool
    {
        $encode = $this->decodings($header);
        foreach ($encode as $coding) {
            if (in_array($coding, $this->contentEncoded)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Extract encodings from its compiled content
     * @param string $headers
     * @return string[]
     */
    protected function decodings(string $headers): array
    {
        if (empty($headers)) {
            return [];
        }
        return array_map(function ($enc) {
            return trim(mb_strtolower($enc));
        }, explode(',', $headers));
    }

    /**
     * In and Out are streams - "file handler" resources
     * @param resource $content
     * @return resource
     */
    abstract public function processDecode($content);
}
