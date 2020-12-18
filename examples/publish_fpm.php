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

include __DIR__ . '/../vendor/autoload.php';

use Swoole\Coroutine;
use Simps\MQTT\Client;

/**
 * 适用于fpm环境下发布信息，指定第四个参数clientType = Client::SYNC_CLIENT_TYPE
 */

$config = [
    'host' => '127.0.0.1',
    'port' => 1883,
    'time_out' => 5,
    'user_name' => 'user001',
    'password' => 'hLXQ9ubnZGzkzf',
    'client_id' => Client::genClientID(),
    'keep_alive' => 20,
];

$client = new Client(
    $config,
    ['open_mqtt_protocol' => true, 'package_max_length' => 2 * 1024 * 1024],
    SWOOLE_SOCK_TCP,
    Client::SYNC_CLIENT_TYPE
);
while (!$client->connect()) {
    sleep(3);
    $client->connect();
}
while (true) {
    $response = $client->publish(
        'simps-mqtt/user001/update',
        '{"time":' . time() . '}',
        1,
        0,
        0,
        [
            'topic_alias' => 1,
            'message_expiry_interval' => 12
        ]
    );
    var_dump($response);
    sleep(3);
}
