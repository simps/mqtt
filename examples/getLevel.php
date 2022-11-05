<?php
/**
 * This file is part of Simps.
 *
 * @link     https://github.com/simps/mqtt
 * @contact  Lu Fei <lufei@simps.io>
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 */

include_once __DIR__ . '/bootstrap.php';

use Simps\MQTT\Protocol\Types;
use Simps\MQTT\Protocol\V3;
use Simps\MQTT\Protocol\V5;
use Simps\MQTT\Tools\UnPackTool;
use Simps\MQTT\Protocol\ProtocolInterface;

$server = new Swoole\Server('127.0.0.1', 1883, SWOOLE_BASE);

$server->set(
    [
        'open_mqtt_protocol' => true,
        'worker_num' => 1,
        'package_max_length' => 2 * 1024 * 1024,
    ]
);

$server->on('connect', function ($server, $fd) {
    echo "Client #{$fd}: Connect.\n";
});

$server->on('receive', function (Swoole\Server $server, $fd, $reactorId, $data) {
    $type = UnPackTool::getType($data);
    if ($type === Types::CONNECT) {
        $level = UnPackTool::getLevel($data);
        $class = $level === ProtocolInterface::MQTT_PROTOCOL_LEVEL_5_0 ? V5::class : V3::class;
        $server->fds[$fd] = ['level' => $level, 'class' => $class];
    }
    /** @var ProtocolInterface $unpack */
    $unpack = $server->fds[$fd]['class'];
    var_dump($unpack::unpack($data));
});

$server->on('close', function ($server, $fd) {
    unset($server->fds[$fd]);
    echo "Client #{$fd}: Close.\n";
});

$server->start();
