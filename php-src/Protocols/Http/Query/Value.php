<?php

namespace kalanis\RemoteRequest\Protocols\Http\Query;


/**
 * Class Value
 * @package kalanis\RemoteRequest\Protocols\Http\Query
 * Single item for query
 */
class Value
{
    protected $content = '';

    public function __construct($content = '')
    {
        $this->content = $content;
    }

    public function setContent($content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getContent(): string
    {
        return (string)$this->content;
    }
}
