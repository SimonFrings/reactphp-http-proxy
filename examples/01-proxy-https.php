<?php

// A simple example which requests https://google.com/ through an HTTP CONNECT proxy.
// The proxy can be given as first argument and defaults to localhost:8080 otherwise.

use Clue\React\HttpProxy\ProxyConnector;
use React\Socket\TcpConnector;
use React\Socket\SecureConnector;
use React\Socket\ConnectionInterface;

require __DIR__ . '/../vendor/autoload.php';

$url = isset($argv[1]) ? $argv[1] : '127.0.0.1:8080';

$loop = React\EventLoop\Factory::create();

$connector = new TcpConnector($loop);
$proxy = new ProxyConnector($url, $connector);
$ssl = new SecureConnector($proxy, $loop);

$ssl->connect('google.com:443')->then(function (ConnectionInterface $stream) {
    $stream->write("GET / HTTP/1.1\r\nHost: google.com\r\nConnection: close\r\n\r\n");
    $stream->on('data', function ($chunk) {
        echo $chunk;
    });
}, 'printf');

$loop->run();
