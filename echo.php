<?php
// Just start this server and connect to it. Everything you send to it will be
// sent back to you.
//
// $ php examples/01-echo.php 8000
// $ telnet localhost 8000
//
// You can also run a secure TLS echo server like this:
//
// $ php examples/01-echo.php 8000 examples/localhost.pem
// $ openssl s_client -connect localhost:8000
//use React\EventLoop\Factory;
//use React\Socket\Server;
use React\Socket\ConnectionInterface;
//use React\Socket\SecureServer;

require PHP_BINDIR.'/vendor/autoload.php';

$loop = React\EventLoop\Factory::create();

$socket = new React\Socket\Server($loop);
$socket->on('connection', function (ConnectionInterface $conn) {
    /*$conn->write("Hello " . $conn->getRemoteAddress() . "!\n");
    $conn->write("Welcome to this amazing server!\n");
    $conn->write("Here's a tip: don't say anything.\n");

    $conn->on('data', function ($data) use ($conn) {
        $conn->close();
    });*/
    echo '[connected]';
    $conn->pipe($conn);
});
//echo 'Listening on ' . $socket->getAddress() . PHP_EOL;
$socket->listen(8000);
$loop->run();