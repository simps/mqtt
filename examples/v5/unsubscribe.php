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

include __DIR__ . '/../../vendor/autoload.php';

use Swoole\Coroutine;
use Simps\MQTT\Client;

$config = [
    'host' => '127.0.0.1',
//    'host' => 'broker.emqx.io',
    'port' => 1883,
    'time_out' => 5,
    'user_name' => 'user001',
    'password' => 'hLXQ9ubnZGzkzf',
    'client_id' => Client::genClientID(),
    'keep_alive' => 20,
    'properties' => [
        'session_expiry_interval' => 213,
        'receive_maximum' => 221,
        'topic_alias_maximum' => 313,
    ],
    'protocol_level' => 5,
];

Coroutine\run(function () use ($config) {
    $client = new Client($config, ['open_mqtt_protocol' => true, 'package_max_length' => 2 * 1024 * 1024]);
    $will = [
        'topic' => 'simps-mqtt/user001/update',
        'qos' => 1,
        'retain' => 0,
        'message' => '' . time(),
    ];
    while (! $client->connect(false, $will)) {
        \Swoole\Coroutine::sleep(3);
        $client->connect(true, $will);
    }
    $topics = ['simps-mqtt/user001/get'];
    $res = $client->unsubscribe($topics);
    var_dump($res);
});
