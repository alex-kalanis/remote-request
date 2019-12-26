<?php

namespace RemoteRequest\Pointers;

use RemoteRequest\RequestException;
use RemoteRequest\Wrappers\AWrapper;

/**
 * Pointer to the local source (file, memory)
 * Good one for testing (put inside the content you want to get)
 */
class SharedInternal extends APointer
{
    /** @var resource|null */
    protected $resourcePointer = null;

    public function getRemotePointer(AWrapper $protocolWrapper)
    {
        if (is_null($this->resourcePointer)) {
            $this->resourcePointer = fopen($protocolWrapper->getHostname(), 'r+');
        }
        if (!$this->resourcePointer) {
            throw new RequestException('Cannot establish connection');
        }
        return $this->resourcePointer;
    }
}
