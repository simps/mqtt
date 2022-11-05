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

/**
 * Support publish in fpm, need to specify clientType as Client::SYNC_CLIENT_TYPE
 */
$client = new Client(SIMPS_MQTT_LOCAL_HOST, SIMPS_MQTT_PORT, getTestMQTT5ConnectConfig(), Client::SYNC_CLIENT_TYPE);

$client->connect();

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
