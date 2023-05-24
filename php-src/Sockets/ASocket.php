<?php

namespace kalanis\RemoteRequest\Sockets;


use kalanis\RemoteRequest\Interfaces\IConnectionParams;
use kalanis\RemoteRequest\Interfaces\IRRTranslations;
use kalanis\RemoteRequest\RequestException;
use kalanis\RemoteRequest\Traits\TLang;


/**
 * Class ASocket
 * @package kalanis\RemoteRequest\Sockets
 * Network sockets to the remote server - base abstract method
 */
abstract class ASocket
{
    use TLang;

    /** @var resource|null */
    protected $pointer = null;

    public function __construct(?IRRTranslations $lang = null)
    {
        $this->setRRLang($lang);
    }

    public function __destruct()
    {
        $this->close();
    }

    public function close(): void
    {
        if (!empty($this->pointer)) {
            fclose($this->pointer);
            $this->pointer = null;
        }
    }

    /**
     * @param IConnectionParams $params
     * @throws RequestException
     * @return resource
     */
    abstract protected function remotePointer(IConnectionParams $params);

    /**
     * @param IConnectionParams $params
     * @throws RequestException
     * @return resource|null
     */
    public function getResourcePointer(IConnectionParams $params)
    {
        if (empty($this->pointer)) {
            $this->pointer = $this->remotePointer($params);
        } else {
            rewind($this->pointer);
        }
        return $this->pointer;
    }
}
