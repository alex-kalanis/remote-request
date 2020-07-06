<?php

namespace RemoteRequest\Wrappers\Fsp;

use RemoteRequest\RequestException;

/**
 * Wrapper to plug FSP info into PHP - directory part
 */
class AOperations
{
    protected $runner = null;

    public function __construct(Runner $runner)
    {
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
            throw new RequestException('Malformed path: ' . $path);
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
