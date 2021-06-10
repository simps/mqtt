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

declare(strict_types=1);

namespace SimpsTest\MQTT\Unit;

use PHPUnit\Framework\TestCase;
use Simps\MQTT\Client as MQTTClient;
use Simps\MQTT\Exception\ConnectException;
use Simps\MQTT\Exception\ProtocolException;
use Simps\MQTT\Protocol\ProtocolInterface;
use Simps\MQTT\Protocol\Types;
use Swoole\Coroutine;

/**
 * @internal
 * @coversNothing
 */
class ClientTest extends TestCase
{
    public function testConnectException()
    {
        try {
            $client = new MQTTClient(SIMPS_MQTT_LOCAL_HOST, SIMPS_MQTT_PORT, getTestConnectConfig());
            $client->connect();
        } catch (\Throwable $ex) {
            $this->assertInstanceOf(ConnectException::class, $ex);
        }
    }

    public function testBase64()
    {
        $topic = 'simps-mqtt/test/base64';
        $base64 = base64_encode(file_get_contents(TESTS_DIR . '/files/wechat.jpg'));
        $client = new MQTTClient(SIMPS_MQTT_REMOTE_HOST, SIMPS_MQTT_PORT, getTestConnectConfig());
        $client->connect(false);
        $client->subscribe([$topic => 0]);

        Coroutine::create(function () use ($topic, $base64) {
            $client = new MQTTClient(SIMPS_MQTT_REMOTE_HOST, SIMPS_MQTT_PORT, getTestConnectConfig());
            $client->connect();
            $client->publish($topic, $base64);
        });

        $buffer = $client->recv();
        $this->assertSame($buffer['type'], Types::PUBLISH);
        $this->assertSame($buffer['topic'], $topic);
        $this->assertSame(strlen($buffer['message']), strlen($base64));
    }

    public function testNonWillWithProtocolException()
    {
        try {
            $client = new MQTTClient(SIMPS_MQTT_REMOTE_HOST, SIMPS_MQTT_PORT, getTestConnectConfig());
            $will = [
                'topic' => '',
                'qos' => ProtocolInterface::MQTT_QOS_0,
                'retain' => ProtocolInterface::MQTT_RETAIN_0,
                'message' => 'message',
            ];
            $client->connect(false, $will);
        } catch (\Throwable $ex) {
            $this->assertInstanceOf(ProtocolException::class, $ex);
            $this->assertSame($ex->getMessage(), 'Topic cannot be empty');
        }
    }

    public function testGetClient()
    {
        $client = new MQTTClient(SIMPS_MQTT_REMOTE_HOST, SIMPS_MQTT_PORT, getTestConnectConfig(), MQTTClient::SYNC_CLIENT_TYPE);
        $this->assertInstanceOf(MQTTClient::class, $client);
        $this->assertInstanceOf(\Swoole\Client::class, $client->getClient());

        $client = new MQTTClient(SIMPS_MQTT_REMOTE_HOST, SIMPS_MQTT_PORT, getTestConnectConfig());
        $this->assertInstanceOf(MQTTClient::class, $client);
        $this->assertInstanceOf(Coroutine\Client::class, $client->getClient());
    }
}
