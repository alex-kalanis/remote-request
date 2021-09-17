<?php

namespace kalanis\RemoteRequest\Protocols\Restful;


use kalanis\RemoteRequest\Protocols;


/**
 * Class Query
 * @package kalanis\RemoteRequest\Protocols\Restful
 * Simple RESTful query to remote source
 */
class Query extends Protocols\Http\Query
{
    public function isInline(): bool
    {
        return false;
    }

    protected function prepareQuery()
    {
        $content = [];
        foreach ($this->content as $key => $item) {
            if ($item instanceof Protocols\Http\Query\File) {
                $content[$key] = [
                    'type' => 'file',
                    'filename' => $item->getFilename(),
                    'mimetype' => $item->getMimeType(),
                    'content64' => base64_encode($item->getContent()),
                ];
            } else {
                $content[$key] = $item->getContent();
            }
        }
        $this->contentQuery = json_encode($content);
        $this->contentLength = mb_strlen($this->contentQuery);
        return $this;
    }
}
