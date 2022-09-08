Remote Request
==============

[![Build Status](https://travis-ci.org/alex-kalanis/remote-request.svg?branch=master)](https://travis-ci.org/alex-kalanis/remote-request)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/alex-kalanis/remote-request/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/alex-kalanis/remote-request/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/alex-kalanis/remote-request/v/stable.svg?v=1)](https://packagist.org/packages/alex-kalanis/remote-request)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.3-8892BF.svg)](https://php.net/)
[![Downloads](https://img.shields.io/packagist/dt/alex-kalanis/remote-request.svg?v1)](https://packagist.org/packages/alex-kalanis/remote-request)
[![License](https://poser.pugx.org/alex-kalanis/remote-request/license.svg?v=1)](https://packagist.org/packages/alex-kalanis/remote-request)
[![Code Coverage](https://scrutinizer-ci.com/g/alex-kalanis/remote-request/badges/coverage.png?b=master&v=1)](https://scrutinizer-ci.com/g/alex-kalanis/remote-request/?branch=master)

Requests for local and remote servers in object way. Contains libraries for querying remote
machines - more universal way than Curl and more verbose than file_get_contents().

The basic philosophy of this package is keep it simple and work with bulks of data, not streams,
although stream variables has been used for passing the options. So no things like EventLoop.

# Installation

```json
{
    "require": {
        "alex-kalanis/remote-request": ">=6.0"
    }
}
```

(Refer to [Composer Documentation](https://github.com/composer/composer/blob/master/doc/00-intro.md#introduction) if you are not
familiar with composer)

# Major changes

 - Version 1 was initial
 - Version 2 separated network layers 2 and 3 - transportation and content protocols
 - Version 3 is packaged for Composer
 - Version 4 has internal structure change after adding "new" socket and protocol.
 - Version 5 changed paths and namespaces, use streams and translations
 - Version 6 changed naming from schema to params and allow to use one connection for passing data there and back

# Usages

RemoteRequest FSocket/Stream
------------------

Basic data sending through network.  In this case using method FSocket. No basic dependencies,
secured connection wants compilation php with ssl. Beware - in that case it's necessary
to have as trusted on machine own unsigned keys! On the other side - it's possible to disable
this check using Helper and setting context params (not advised).

Basic usage (http query):

```php
    $libParams = new RemoteRequest\Connection\Params\Ssl();
    $libParams->setTarget('10.0.0.1', 2048);

    $libQuery = new RemoteRequest\Protocols\Http\Query(); # http internals
    $libQuery
        ->setMultipart(true)
        ->setMethod('post')
        ->setRequestSettings($libParams)
        ->setPath('/api/hook/')
        ->addValues([
            'service_id' => $serviceId,
            'hook_data' => $data,
        ])
    ;

    $libProcessor = new RemoteRequest\Connection\Processor(); # tcp/ip http/ssl
    $libProcessor->setProtocolSchema($libSchema);
    $libProcessor->setData($libQuery);

    $libHttpAnswer = new RemoteRequest\Protocols\Http\Answer();
    $response = $libHttpAnswer->setResponse($libProcessor->getResponse());
    return $response->getContent();
```

```php
    return RemoteRequest\Helper::getRemoteContent(
        'https://10.0.0.1:2048/api/hook/',
        [
            'service_id' => $serviceId,
            'hook_data' => $data,
        ], [
            'method' => 'post',
            'multipart' => true,
        ]
    );
```

Variant for UDP
```php
    $libParams = new RemoteRequest\Connection\Params\Udp(); # query params on layer 3
    $libParams->setTarget('udp-listener.' . DOMAIN, 514);

    $message = new RemoteRequest\Protocols\Dummy\Query();
    $message->maxLength = 0; // expects no response
    $message->body = 'Post message to them!';

    $libProtocol = new RemoteRequest\Connection\Processor();
    $libProtocol->setProtocolSchema($libParams)->setData($message);
    $libProtocol->getResponse(); // just execute
```

```php
    RemoteRequest\Helper::getRemoteContent(
        'udp://udp-listener.' . DOMAIN . ':514',
        'Post message to them!'
    );
```

Thanks to the inheritance it's possible to make a tons of interesting changes. For change
targeting it's possible to set it directly or make a child and set connection params there.
Next - there is possible by only exchange of result classes process XML or JSON.

Operator (both FSocket and Stream) send agent "php-agent/1.3", but it is also possible
to change it.

Connection Params
--------

Contains basic information about method of transferring on network layer level 2 and
destined target of query - usually address and port. Also have other things necessary
to connect another machine like schema and timeout.

### Params UDP

Send it through UDP protocol.

No thanks to the troubles with testing it also contains 2 files for checking connection
on local machine, slurped somewhere on StackOverflow. For using this you need 2 terminal
windows - one for server and another for client. You write messages on client. If messages
has not been shown on both windows there is dead connection inside your ma machine and it
will have problems also with connecting external targets - and still it might be set right.

### Params TCP / HTTP / SSL

Basically variants which send data through tcp protocol. Tcp and Http are in unsecured,
SSL is secured (depends on php if its compiled with ssl support or defined own stream
which pass this obstacle). Http and SSL also adds Http headers.

### Params PHP internals - File, Php

Inside the params there is 2 for accessing internal sources. They are meant for testing
purposes. It is possible to test access to data and they did not need to be saved on external
machine.

Pointers
--------

Nothing so fancy, but just only sources of pointers from stream processors. Both on remote
machine and/or local storage.

### Socket

The most specific one. Usable mainly for connecting with UDP schema. It does not need to wait
after receive data packet which happens with others.

### FSocket, PFsocket

The most stupid ones and most known ones. You cannot convice them with context about your
truth like "That connection IS correctly secured".

### Stream

Stream inside PHP. Can use context params. But for sanity it did not get anything from higher
manipulation functions; even if that works for it.

### Shared internal

Local for testing purposes. It should got some wrapper from internals. Then it's possible
to save data there which got only pointer from PHP.

Protocols
---------

There is defined a few basic protocols and their helpers which makes life with them easier.
On the top there is examples of processing HTTP and UDP. Also it contains a base for querying
REST APIs.

### Restful

Extended, edited HTTP, which in message body has a JSON data package instead of normal bulk
of HTTP data. It can also pass files - uses base64 for transfer. But it cannot compile it back
due unknown definition of data which came from the server.

### FSP

File Sharing Protocol a.k.a. FTP-over-UDP. Old, hackish, slow, but interesting protocol,
which shows that there is no problem with making anything readable what is set into the
files in layers. You could find more about it online. Here is simple wrapper for PHP which
allows you use it transparently.

Tests
-----

Uses PhpUnit tests. Download Phpunit.phar, save it to the root, make it executable and run.
There is excluded directory - Wrappers. They're here to access remote sources and simplify
your life, so it isn't good idea to run tests on them. Also Helper isn't covered for same
reason.

Not PSR-7
---------

This library is not compliant with PSR-7 and it has a few reasons. At first the PSR-7 has
been made with HTTP in mind. Then it got streams and totally discarded the filling of the
usually sent body. Some things are specific for HTTP and in other protocols are unwelcomed.
I now write about schema:host:port and form inputs.

If you really want to know more, try to implement FSP or SMB connectors via PSR-7. You will
get a lot of headache. Or HTTP2/3, where the content is binary-encoded and runs over udp
schema (in case of 3). You WILL have a lots of problems implement that. Not with Remote
Request, where the responsibilities stays separated.
