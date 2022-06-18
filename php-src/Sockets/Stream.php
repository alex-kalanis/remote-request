<?php

namespace kalanis\RemoteRequest\Sockets;


use kalanis\RemoteRequest\RequestException;
use kalanis\RemoteRequest\Schemas\ASchema;


/**
 * Class Stream
 * @package kalanis\RemoteRequest\Sockets
 * Network pointer to the remote server - method Stream
 * Because that little shit cannot set context options to Fsocket; namely devel certs
 */
class Stream extends ASocket
{
    /** @var array<string, string|array<string, string>> */
    protected $contextOptions = [];

    /**
     * @param array<string, string|array<string, string>> $contextOptions
     * @return $this
     */
    public function setContextOptions(array $contextOptions = []): self
    {
        $this->contextOptions = $contextOptions;
        return $this;
    }

    /**
     * @param ASchema $protocolWrapper
     * @throws RequestException
     * @return resource
     * @codeCoverageIgnore because accessing remote source
     */
    protected function remotePointer(ASchema $protocolWrapper)
    {
        // Make the request to the server
        // If possible, securely post using HTTPS, your PHP server will need to be SSL enabled

        // example array of context options for skipping devel certs
//        $contextOptions = [
//            'ssl' => [
//                'verify_peer' => !DEVEL_ENVIRONMENT, // You could skip all of the trouble by changing this to false, but it's WAY uncool for security reasons. // kecy...
//                'cafile' => '/etc/ssl/certs/cacert.pem',
//                'CN_match' => 'example.com', // Change this to your certificates Common Name (or just comment this line out if not needed)
//                'ciphers' => 'HIGH:!SSLv2:!SSLv3',
//                'disable_compression' => true,
//            ],
//        ];

        $context = stream_context_create($this->contextOptions);
        $link = $protocolWrapper->getHostname() . (!empty($protocolWrapper->getPort()) ? ':' . $protocolWrapper->getPort() : '' );
        $filePointer = stream_socket_client($link, $errno, $errStr, $protocolWrapper->getTimeout(), STREAM_CLIENT_CONNECT, $context);

        if (!$filePointer) {
            throw new RequestException($this->lang->rrSocketCannotConnect());
        }
        return $filePointer;
    }
}
