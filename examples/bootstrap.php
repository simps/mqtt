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

use Simps\MQTT\Config\ClientConfig;

const SSL_CERTS_DIR = __DIR__ . '/ssl_certs';

const SWOOLE_MQTT_CONFIG = [
    'open_mqtt_protocol' => true,
    'package_max_length' => 2 * 1024 * 1024,
    'connect_timeout' => 1.0,
    'write_timeout' => 3.0,
    'read_timeout' => 0.5,
];

const SIMPS_MQTT_LOCAL_HOST = '127.0.0.1';
const SIMPS_MQTT_REMOTE_HOST = 'broker.emqx.io';
const SIMPS_MQTT_PORT = 1883;

function getTestConnectConfig()
{
    $config = new ClientConfig();
    return $config->setUserName('username')
        ->setPassword('password')
        ->setClientId(\Simps\MQTT\Client::genClientID())
        ->setKeepAlive(10)
        ->setDelay(3000) // 3s
        ->setMaxAttempts(5)
        ->setSwooleConfig(SWOOLE_MQTT_CONFIG);
}

function getTestMQTT5ConnectConfig()
{
    $config = new ClientConfig();
    return $config->setUserName('username')
        ->setPassword('password')
        ->setClientId(\Simps\MQTT\Client::genClientID())
        ->setKeepAlive(10)
        ->setDelay(3000) // 3s
        ->setMaxAttempts(5)
        ->setProperties([
            'session_expiry_interval' => 60,
            'receive_maximum' => 65535,
            'topic_alias_maximum' => 65535,
        ])
        ->setProtocolLevel(5)
        ->setSwooleConfig(SWOOLE_MQTT_CONFIG);
}
