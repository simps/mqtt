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

include __DIR__ . '/bootstrap.php';

use Simps\MQTT\Client;
use Simps\MQTT\Protocol\Types;
use Swoole\Coroutine;

Coroutine\run(function () {
    $client = new Client(SIMPS_MQTT_LOCAL_HOST, SIMPS_MQTT_PORT, getTestConnectConfig());
    $will = [
        'topic' => 'simps-mqtt/user001/delete',
        'qos' => 0,
        'retain' => 0,
        'message' => 'byebye',
    ];
    $client->connect(true, $will);
    $topics['simps-mqtt/user001/get'] = 0;
    $topics['simps-mqtt/user001/update'] = 1;
    $client->subscribe($topics);
    $timeSincePing = time();
    while (true) {
        try {
            $buffer = $client->recv();
            if ($buffer && $buffer !== true) {
                var_dump($buffer);
                // QoS1 PUBACK
                if ($buffer['type'] === Types::PUBLISH && $buffer['qos'] === 1) {
                    $client->send(
                        [
                            'type' => Types::PUBACK,
                            'message_id' => $buffer['message_id'],
                        ],
                        false
                    );
                }
                if ($buffer['type'] === Types::DISCONNECT) {
                    echo "Broker is disconnected\n";
                    $client->close();
                    break;
                }
            }
            if ($timeSincePing <= (time() - $client->getConfig()->getKeepAlive())) {
                $buffer = $client->ping();
                if ($buffer) {
                    echo 'send ping success' . PHP_EOL;
                    $timeSincePing = time();
                }
            }
        } catch (\Throwable $e) {
            throw $e;
        }
    }
});
