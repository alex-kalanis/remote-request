<?php

namespace kalanis\RemoteRequest\Protocols\Http\Query;


/**
 * Class Value
 * @package kalanis\RemoteRequest\Protocols\Http\Query
 * Single item for query
 */
class Value
{
    /** @var mixed */
    protected $content = '';

    /**
     * @param mixed $content
     */
    public function __construct($content = '')
    {
        $this->content = $content;
    }

    /**
     * @param mixed $content
     * @return $this
     */
    public function setContent($content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getContent(): string
    {
        return strval($this->content);
    }
}
