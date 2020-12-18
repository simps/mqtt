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

use Simps\MQTT\Client;

/**
 * Support publish in fpm, need to specify clientType as Client::SYNC_CLIENT_TYPE
 */
$client = new Client(getTestMQTT5ConnectConfig(), SWOOLE_MQTT_CONFIG, SWOOLE_SOCK_TCP, Client::SYNC_CLIENT_TYPE);

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
            'message_expiry_interval' => 10,
        ]
    );
    var_dump($response);
    sleep(3);
}
