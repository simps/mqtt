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

include __DIR__ . '/../bootstrap.php';

use Simps\MQTT\Protocol\Types;
use Simps\MQTT\Protocol\V5;
use Simps\MQTT\Tools\Common;

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

$server->on('receive', function (Swoole\Server $server, $fd, $from_id, $data) {
    try {
        // debug
//        Common::printf($data);
        $data = V5::unpack($data);
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
                        V5::pack(
                            [
                                'type' => Types::CONNACK,
                                'code' => 0,
                                'session_present' => 0,
                                'properties' => [
                                    'maximum_packet_size' => 1048576,
                                    'retain_available' => true,
                                    'shared_subscription_available' => true,
                                    'subscription_identifier_available' => true,
                                    'topic_alias_maximum' => 65535, //0
                                    'wildcard_subscription_available' => true,
                                ],
                            ]
                        )
                    );
                    break;
                case Types::PINGREQ:
                    $server->send($fd, V5::pack(['type' => Types::PINGRESP]));
                    break;
                case Types::DISCONNECT:
                    if ($server->exist($fd)) {
                        $server->close($fd);
                    }
                    break;
                case Types::PUBLISH:
                    // Send to subscribers
                    foreach ($server->connections as $sub_fd) {
                        if ($sub_fd != $fd) {
                            $server->send(
                                $sub_fd,
                                V5::pack(
                                    [
                                        'type' => $data['type'],
                                        'topic' => $data['topic'],
                                        'message' => $data['message'],
                                        'dup' => $data['dup'],
                                        'qos' => $data['qos'],
                                        'retain' => $data['retain'],
                                        'message_id' => $data['message_id'] ?? 0,
                                    ]
                                )
                            );
                        }
                    }

                    if ($data['qos'] === 1) {
                        $server->send(
                            $fd,
                            V5::pack(
                                [
                                    'type' => Types::PUBACK,
                                    'message_id' => $data['message_id'] ?? 0,
                                ]
                            )
                        );
                    }

                    break;
                case Types::SUBSCRIBE:
                    $payload = [];
                    foreach ($data['topics'] as $k => $option) {
                        $qos = $option['qos'];
                        if (is_numeric($qos) && $qos < 3) {
                            $payload[] = $qos;
                        } else {
                            $payload[] = \Simps\MQTT\Hex\ReasonCode::QOS_NOT_SUPPORTED;
                        }
                    }
                    $server->send(
                        $fd,
                        V5::pack(
                            [
                                'type' => Types::SUBACK,
                                'message_id' => $data['message_id'] ?? 0,
                                'codes' => $payload,
                            ]
                        )
                    );
                    break;
                case Types::UNSUBSCRIBE:
                    $payload = [];
                    foreach ($data['topics'] as $k => $qos) {
                        if (is_numeric($qos) && $qos < 3) {
                            $payload[] = $qos;
                        } else {
                            $payload[] = 0x80;
                        }
                    }
                    $server->send(
                        $fd,
                        V5::pack(
                            [
                                'type' => Types::UNSUBACK,
                                'message_id' => $data['message_id'] ?? 0,
                                'codes' => $payload,
                            ]
                        )
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
