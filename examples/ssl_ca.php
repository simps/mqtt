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
    $config = [
        'host' => 'test.mosquitto.org',
        'port' => 8883,
        'user_name' => '',
        'password' => '',
        'client_id' => Client::genClientID(),
        'keep_alive' => 20,
    ];

    $swooleConfig = [
        'open_mqtt_protocol' => true,
        'package_max_length' => 2 * 1024 * 1024,
        'ssl_cafile' => SSL_CERTS_DIR . '/mosquitto.org.crt', // https://test.mosquitto.org/ssl/mosquitto.org.crt
        'ssl_allow_self_signed' => true,
        'ssl_verify_peer' => true,
    ];

    $client = new Client($config, $swooleConfig, SWOOLE_SOCK_TCP | SWOOLE_SSL);

    while (!$data = $client->connect()) {
        Coroutine::sleep(3);
        $client->connect();
    }
    $topics['testtopic/#'] = 0;
    $timeSincePing = time();
    $client->subscribe($topics);
    while (true) {
        $buffer = $client->recv();
        var_dump($buffer);
        if ($buffer && $buffer !== true) {
            $timeSincePing = time();
        }
        if (isset($config['keep_alive']) && $timeSincePing < (time() - $config['keep_alive'])) {
            $buffer = $client->ping();
            if ($buffer) {
                echo 'send ping success' . PHP_EOL;
                $timeSincePing = time();
            } else {
                $client->close();
                break;
            }
        }
    }
});
