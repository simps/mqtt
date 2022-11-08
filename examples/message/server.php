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

include_once __DIR__ . '/../bootstrap.php';

use Simps\MQTT\Protocol\Types;
use Simps\MQTT\Protocol\V3;
use Simps\MQTT\Tools\Common;
use Simps\MQTT\Message\ConnAck;
use Simps\MQTT\Message\Publish;
use Simps\MQTT\Message\PubAck;
use Simps\MQTT\Message\SubAck;
use Simps\MQTT\Message\UnSubAck;
use Simps\MQTT\Message\PingResp;

$server = new Swoole\Server('127.0.0.1', 1883, SWOOLE_BASE);

$server->set(
    [
        'open_mqtt_protocol' => true,
        'worker_num' => 2,
        'package_max_length' => 2 * 1024 * 1024,
    ]
);

$server->on('connect', function ($server, $fd) {
    echo "Client #{$fd}: Connect.\n";
});

$server->on('receive', function (Swoole\Server $server, $fd, $reactorId, $data) {
    try {
        // debug
//        Common::printf($data);
        $data = V3::unpack($data);
        if (is_array($data) && isset($data['type'])) {
            switch ($data['type']) {
                case Types::CONNECT:
                    // Check protocol_name
                    if ($data['protocol_name'] != 'MQTT') {
                        $server->close($fd);

                        return false;
                    }

                    // Check connection information, etc.

                    $server->send(
                        $fd,
                        (new ConnAck())->setCode(0)
                        ->setSessionPresent(0)
                    );
                    break;
                case Types::PINGREQ:
                    $server->send($fd, (new PingResp()));
                    break;
                case Types::DISCONNECT:
                    if ($server->exist($fd)) {
                        $server->close($fd);
                    }
                    break;
                case Types::PUBLISH:
                    // Send to subscribers
                    foreach ($server->connections as $sub_fd) {
                            $server->send(
                                $sub_fd,
                                (new Publish())->setTopic($data['topic'])
                                ->setMessage($data['message'])
                                ->setDup($data['dup'])
                                ->setQos($data['qos'])
                                ->setRetain($data['retain'])
                                ->setMessageId($data['message_id'] ?? 0)
                            );
                    }

                    if ($data['qos'] === 1) {
                        $server->send(
                            $fd,
                            (new PubAck())->setMessageId($data['message_id'])
                        );
                    }

                    break;
                case Types::SUBSCRIBE:
                    $payload = [];
                    foreach ($data['topics'] as $qos) {
                        if (is_numeric($qos) && $qos < 3) {
                            $payload[] = $qos;
                        } else {
                            $payload[] = 0x80;
                        }
                    }
                    $server->send(
                        $fd,
                        (new SubAck())->setMessageId($data['message_id'] ?? 0)
                            ->setCodes($payload)
                    );
                    break;
                case Types::UNSUBSCRIBE:
                    $server->send(
                        $fd,
                        (new UnSubAck())->setMessageId($data['message_id'] ?? 0)
                    );
                    break;
            }
        } else {
            $server->close($fd);
        }
    } catch (\Throwable $e) {
        echo "\033[0;31mError: {$e->getMessage()}\033[0m\r\n";
        $server->close($fd);
    }
});

$server->on('close', function ($server, $fd) {
    echo "Client #{$fd}: Close.\n";
});

$server->start();
