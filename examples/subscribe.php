<?php
/**
 * This file is part of Simps
 *
 * @link     https://github.com/simps/mqtt
 * @contact  Lu Fei <lufei@simps.io>
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code
 */

include __DIR__ . '/bootstrap.php';

use Simps\MQTT\Client;
use Swoole\Coroutine;

Coroutine\run(function () {
    $client = new Client(SIMPS_MQTT_LOCAL_HOST, SIMPS_MQTT_PORT, getTestConnectConfig());
    $will = [
        'topic' => 'simps-mqtt/user001/update',
        'qos' => 1,
        'retain' => 0,
        'message' => 'byebye',
    ];
    while (!$client->connect(false, $will)) {
        Coroutine::sleep(3);
        $client->connect(true, $will);
    }
    $topics['simps-mqtt/user001/get'] = 0;
    $topics['simps-mqtt/user001/update'] = 1;
    $timeSincePing = time();
    $client->subscribe($topics);
    while (true) {
        $buffer = $client->recv();
        var_dump($buffer);
        if ($buffer && $buffer !== true) {
            $timeSincePing = time();
        }
        if ($timeSincePing < (time() - $client->getConfig()->getKeepAlive())) {
            $buffer = $client->ping();
            if ($buffer) {
                echo 'send ping success' . PHP_EOL;
                $timeSincePing = time();
            } else {
                $client->close();
                break;
            }
        }
    }
});
