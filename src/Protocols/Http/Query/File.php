<?php

namespace RemoteRequest\Protocols\Http\Query;

/**
 * Single item for query - file
 * Beware: The content must be already loaded into memory, not stay on volume; there is no additional loading (for reasons)
 */
class File extends Value
{
    public $filename = 'binary';
    public $mime = 'octet/stream';

    public function getFilename(): string
    {
        return '' . $this->filename;
    }

    public function getMimeType(): string
    {
        return '' . $this->mime;
    }
}