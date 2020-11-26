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

$config = [
    'host' => 'test.mosquitto.org',
    'port' => 8883,
    'time_out' => 5,
    'user_name' => '',
    'password' => '',
    'client_id' => Client::genClientID(),
    'keep_alive' => 20,
];

Coroutine\run(
    function () use ($config) {
        $client = new Client(
            $config, [
            'open_mqtt_protocol' => true,
            'package_max_length' => 2 * 1024 * 1024,
            'ssl_cafile' => __DIR__ . '/mosquitto.org.crt',
            'ssl_allow_self_signed' => true,
            'ssl_verify_peer' => true,
        ], SWOOLE_SOCK_TCP | SWOOLE_SSL
        );
        $will = [
            'topic' => 'testtopic/#',
            'qos' => 1,
            'retain' => 0,
            'message' => '' . time(),
        ];
        while (!$client->connect(false, $will)) {
            \Swoole\Coroutine::sleep(3);
            $client->connect(true, $will);
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
    }
);
