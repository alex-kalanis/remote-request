<?php

namespace kalanis\RemoteRequest\Wrappers\Fsp;


use kalanis\RemoteRequest\Interfaces\IRRTranslations;
use kalanis\RemoteRequest\RequestException;
use kalanis\RemoteRequest\Traits\TLang;


/**
 * Class AOperations
 * @package kalanis\RemoteRequest\Wrappers\Fsp
 * Wrapper to plug FSP info into PHP - directory part
 */
class AOperations
{
    use TLang;

    /** @var Runner */
    protected $runner = null;

    public function __construct(Runner $runner, IRRTranslations $lang)
    {
        $this->setRRLang($lang);
        $this->runner = $runner;
    }

    /**
     * @param string $path
     * @param bool $setTarget
     * @throws RequestException
     * @return string
     */
    protected function parsePath(string $path, bool $setTarget = true): string
    {
        $host = parse_url($path, PHP_URL_HOST);
        $port = parse_url($path, PHP_URL_PORT);
        $into = parse_url($path, PHP_URL_PATH);
        if (empty($host) || empty($into)) {
            throw new RequestException($this->getRRLang()->rrFspWrapMalformedPath($path));
        }
        if ($setTarget) {
            $this->runner->getConnectParams()->setTarget(
                $host,
                !empty($port) ? intval($port) : 21,
                $this->runner->getTimeout($host)
            );
        }
        $pre = (in_array($into[0], ['.', '\\'])) ? substr($into, 1) : $into ;
        return $pre;
    }
}
