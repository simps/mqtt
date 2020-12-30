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
    'connect_timeout' => 1.0,
    'write_timeout' => 3.0,
    'read_timeout' => 0.5,
];

function getTestConnectConfig(bool $isLocal = true, string $host = 'broker.emqx.io')
{
    if ($isLocal) {
        $host = '127.0.0.1';
    }

    return [
        'host' => $host,
        'port' => 1883,
        'user_name' => 'username',
        'password' => 'password',
        'client_id' => \Simps\MQTT\Client::genClientID(),
        'keep_alive' => 10,
    ];
}

function getTestMQTT5ConnectConfig(bool $isLocal = true, string $host = 'broker.emqx.io')
{
    if ($isLocal) {
        $host = '127.0.0.1';
    }

    return [
        'host' => $host,
        'port' => 1883,
        'user_name' => 'username',
        'password' => 'password',
        'client_id' => \Simps\MQTT\Client::genClientID(),
        'keep_alive' => 10,
        'properties' => [
            'session_expiry_interval' => 60,
            'receive_maximum' => 65535,
            'topic_alias_maximum' => 65535,
        ],
        'protocol_level' => 5,
    ];
}
