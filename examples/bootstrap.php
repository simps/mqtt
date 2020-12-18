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

foreach (
    [
        __DIR__ . '/../vendor/autoload.php',
        __DIR__ . '/vendor/autoload.php',
        __DIR__ . '/../../../autoload.php',
    ] as $file
) {
    if (file_exists($file)) {
        require $file;
        break;
    }
}

const SSL_CERTS_DIR = __DIR__ . '/ssl_certs';

const SWOOLE_MQTT_CONFIG = [
    'open_mqtt_protocol' => true,
    'package_max_length' => 2 * 1024 * 1024,
];

function getTestConnectConfig(string $host = '127.0.0.1')
{
    return [
        'host' => $host,
        'port' => 1883,
        'time_out' => 5,
        'user_name' => 'username',
        'password' => 'password',
        'client_id' => \Simps\MQTT\Client::genClientID(),
        'keep_alive' => 20,
    ];
}

function getTestMQTT5ConnectConfig(string $host = '127.0.0.1')
{
    return [
        'host' => $host,
        'port' => 1883,
        'time_out' => 5,
        'user_name' => 'username',
        'password' => 'password',
        'client_id' => \Simps\MQTT\Client::genClientID(),
        'keep_alive' => 20,
        'properties' => [
            'session_expiry_interval' => 60,
            'receive_maximum' => 65535,
            'topic_alias_maximum' => 65535,
        ],
        'protocol_level' => 5,
    ];
}
