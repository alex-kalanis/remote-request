<?php

namespace kalanis\RemoteRequest\Protocols\Http\Query;


use kalanis\RemoteRequest\Protocols\Helper;


/**
 * Class File
 * @package kalanis\RemoteRequest\Protocols\Http\Query
 * Single item for query - file
 * Beware: The content must be already loaded into memory, not stay on volume; there is no additional loading (for reasons)
 */
class File extends Value
{
    public $filename = 'binary';
    public $mime = 'octet/stream';

    public function getFilename(): string
    {
        return (string)$this->filename;
    }

    public function getMimeType(): string
    {
        return (string)$this->mime;
    }

    public function getContent(): string
    {
        return is_resource($this->content)
            ? strval(stream_get_contents($this->content, -1, 0))
            : strval($this->content)
        ;
    }

    /**
     * @return resource
     */
    public function getStream()
    {
        if (is_resource($this->content)) {
            return $this->content;
        }
        $stream = Helper::getMemStorage();
        fwrite($stream, strval($this->content));
        rewind($stream);
        return $stream;
    }
}
