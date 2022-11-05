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

include_once __DIR__ . '/bootstrap.php';

use Simps\MQTT\Client;
use Simps\MQTT\Config\ClientConfig;
use Swoole\Coroutine;

Coroutine\run(function () {
    $swooleConfig = [
        'open_mqtt_protocol' => true,
        'package_max_length' => 2 * 1024 * 1024,
        'ssl_allow_self_signed' => true,
        'ssl_verify_peer' => true,
        'ssl_cafile' => SSL_CERTS_DIR . '/mosquitto.org.crt', // https://test.mosquitto.org/ssl/mosquitto.org.crt
        'ssl_key_file' => SSL_CERTS_DIR . '/client.key', // Please go to https://test.mosquitto.org/ssl to generate.
        'ssl_cert_file' => SSL_CERTS_DIR . '/client.crt', // Please go to https://test.mosquitto.org/ssl to generate.
    ];

    $config = new ClientConfig();
    $config->setClientId(Client::genClientID())
        ->setKeepAlive(20)
        ->setUserName('')
        ->setPassword('')
        ->setDelay(3000) // 3s
        ->setMaxAttempts(5)
        ->setSwooleConfig($swooleConfig)
        ->setSockType(SWOOLE_SOCK_TCP | SWOOLE_SSL);

    $client = new Client('test.mosquitto.org', 8884, $config);
    $client->connect();
    $topics['testtopic/#'] = 0;
    $client->subscribe($topics);
    $timeSincePing = time();
    while (true) {
        $buffer = $client->recv();
        if ($buffer && $buffer !== true) {
            var_dump($buffer);
            $timeSincePing = time();
        }
        if ($timeSincePing <= (time() - $config->getKeepAlive())) {
            $buffer = $client->ping();
            if ($buffer) {
                echo 'send ping success' . PHP_EOL;
                $timeSincePing = time();
            }
        }
    }
});
