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
    $client->connect();
    while (true) {
        $response = $client->publish('simps-mqtt/user001/update', '{"time":' . time() . '}', 1);
        var_dump($response);
        Coroutine::sleep(3);
    }
});
