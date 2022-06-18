<?php

namespace kalanis\RemoteRequest\Protocols\Http\Answer\DecodeStrings;


/**
 * Class ADecoder
 * @package kalanis\RemoteRequest\Protocols\Http\Answer\DecodeStrings
 * Encode content processable in strings
 * Due changes by content encoding there shall be a way to expand to the original content
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Encoding
 */
abstract class ADecoder
{
    /** @var string[] */
    protected $contentEncoded = [];

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
     * Extract encodings from its compiled content header
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
     * In and Out are strings - everything in memory
     * @param string $content
     * @return string
     */
    abstract public function processDecode(string $content): string;
}
