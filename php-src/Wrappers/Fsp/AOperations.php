<?php

namespace kalanis\RemoteRequest\Wrappers\Fsp;


use kalanis\RemoteRequest\Interfaces\IRRTranslations;
use kalanis\RemoteRequest\RequestException;


/**
 * Class AOperations
 * @package kalanis\RemoteRequest\Wrappers\Fsp
 * Wrapper to plug FSP info into PHP - directory part
 */
class AOperations
{
    protected $lang = null;
    protected $runner = null;

    public function __construct(IRRTranslations $lang, Runner $runner)
    {
        $this->lang = $lang;
        $this->runner = $runner;
    }

    /**
     * @param string $path
     * @param bool $setTarget
     * @return string
     * @throws RequestException
     */
    protected function parsePath(string $path, bool $setTarget = true): string
    {
        $host = parse_url($path, PHP_URL_HOST);
        $port = parse_url($path, PHP_URL_PORT);
        $into = parse_url($path, PHP_URL_PATH);
        if (empty($host)) {
            throw new RequestException($this->lang->rrFspWrapMalformedPath($path));
        }
        if ($setTarget) {
            $this->runner->getSchema()->setTarget(
                $host,
                !empty($port) ? (int)$port : 21,
                $this->runner->getTimeout($host)
            );
        }
        $pre = (in_array($into[0], ['.', '\\'])) ? substr($into, 1) : $into ;
        return $pre;
    }
}
