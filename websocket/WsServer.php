<?php

declare(strict_types=1);

namespace Websocket;

use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer as RatchetWsServer;

class WsServer
{
    public static function run(string $host, int $port): void
    {
        $server = IoServer::factory(
            new HttpServer(
                new RatchetWsServer(
                    new WsHub()
                )
            ),
            $port,
            $host
        );

        echo "WebSocket ouvindo em ws://{$host}:{$port}\n";
        $server->run();
    }
}
