<?php
/**
 * This file is part of Simps
 *
 * @link     https://github.com/simps/mqtt
 * @contact  lufei <lufei@simps.io>
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code
 */

include __DIR__ . '/../vendor/autoload.php';

use Swoole\Coroutine;
use Simps\MQTT\Client;

$config = [
    'host' => '127.0.0.1',
    'port' => 1883,
    'time_out' => 5,
    'user_name' => 'user001',
    'password' => 'hLXQ9ubnZGzkzf',
    'client_id' => 'd812edc1-18da-2085-0edf-a4a588c296d1',
    'keep_alive' => 20,
];

Coroutine\run(function () use ($config) {
    $client = new Client($config, ['open_mqtt_protocol' => true, 'package_max_length' => 2 * 1024 * 1024]);
    while (! $client->connect()) {
        Coroutine::sleep(3);
        $client->connect();
    }
    while (true) {
        $response = $client->publish('simps-mqtt/user001/update', '{"time":'. time() .'}', 1);
        var_dump($response);
        Coroutine::sleep(3);
    }
});
