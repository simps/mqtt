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

use Simps\MQTT\Client;
use Simps\MQTT\Hex\ReasonCode;
use Simps\MQTT\Protocol\Types;
use Swoole\Coroutine;

Coroutine\run(function () {
    $client = new Client(SIMPS_MQTT_LOCAL_HOST, SIMPS_MQTT_PORT, getTestMQTT5ConnectConfig());
    $will = [
        'topic' => 'simps-mqtt/user001/delete',
        'qos' => 1,
        'retain' => 0,
        'message' => 'byebye',
        'properties' => [
            'will_delay_interval' => 60,
            'message_expiry_interval' => 60,
            'content_type' => 'test',
            'payload_format_indicator' => true, // false 0 1
        ],
    ];
    $client->connect(true, $will);
    $topics['simps-mqtt/user001/get'] = [
        'qos' => 1,
        'no_local' => true,
        'retain_as_published' => true,
        'retain_handling' => 2,
    ];
    $topics['simps-mqtt/user001/update'] = [
        'qos' => 2,
        'no_local' => false,
        'retain_as_published' => true,
        'retain_handling' => 2,
    ];
    $res = $client->subscribe($topics);
    $timeSincePing = time();
    var_dump($res);
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
                    echo sprintf(
                        "Broker is disconnected, The reason is %s [%d]\n",
                        ReasonCode::getReasonPhrase($buffer['code']),
                        $buffer['code']
                    );
                    $client->close($buffer['code']);
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
