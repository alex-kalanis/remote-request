<?php

namespace RemoteRequest\Wrappers\Fsp;

use RemoteRequest;
use RemoteRequest\Protocols\Fsp as Protocol;

/**
 * Wrapper to plug FSP info into PHP - directory part
 */
class AOperations
{
    protected $runner = null;

    public function __construct(Protocol\Runner $runner)
    {
        $this->runner = $runner;
    }

    /**
     * @param string $path
     * @param bool $setTarget
     * @return string
     * @throws RemoteRequest\RequestException
     */
    protected function parsePath(string $path, bool $setTarget = true): string
    {
        $requestUrl = parse_url($path);
        if (false === $requestUrl) {
            throw new RemoteRequest\RequestException('Malformed path: ' . $path);
        }
        if ($setTarget) {
            $this->runner->getSchema()->setTarget(
                $requestUrl['host'],
                !empty($requestUrl['port']) ? (int)$requestUrl['port'] : 21,
                $this->runner->getTimeout($requestUrl['host'])
            );
        }
        $pre = (in_array($requestUrl['path'][0], ['.', '\\'])) ? substr($requestUrl['path'], 1) : $requestUrl['path'] ;
        return $pre;
    }
}
