<?php

declare(strict_types=1);
/**
 * This file is part of Simps.
 *
 * @link     https://github.com/simps/mqtt
 * @contact  Lu Fei <lufei@simps.io>
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 */
namespace SimpsTest\MQTT\V5;

use PHPUnit\Framework\TestCase;
use Simps\MQTT\Exception\ProtocolException;
use Simps\MQTT\Hex\ReasonCode;
use Simps\MQTT\Protocol\Types;
use Simps\MQTT\WebSocketClient;
use Swoole\Coroutine;

/**
 * @internal
 * @coversNothing
 */
class WebSocketTest extends TestCase
{
    private static $topic = '';

    private static $client;

    public static function setUpBeforeClass(): void
    {
        self::$topic = 'testtopic/simps-' . rand(100, 999);
        self::$client = new WebSocketClient(SIMPS_MQTT_REMOTE_HOST, SIMPS_MQTT_OVER_WEBSOCKET_PORT, getTestMQTT5ConnectConfig());
    }

    public static function tearDownAfterClass(): void
    {
        self::$topic = '';
        self::$client = null;
    }

    public function testConnect()
    {
        $res = self::$client->connect();
        $this->assertIsArray($res);
        $this->assertSame(Types::CONNACK, $res['type']);
    }

    /**
     * @depends testConnect
     */
    public function testSubscribe()
    {
        $topics = [
            self::$topic . '/get' => [
                'qos' => 1,
                'no_local' => true,
                'retain_as_published' => true,
                'retain_handling' => 2,
            ],
            self::$topic . '/update' => [
                'qos' => 2,
                'no_local' => false,
                'retain_as_published' => true,
                'retain_handling' => 2,
            ],
        ];
        $res = self::$client->subscribe($topics);
        $this->assertIsArray($res);
        $this->assertSame(Types::SUBACK, $res['type']);
        $this->assertIsArray($res['codes']);
        $this->assertSame(ReasonCode::GRANTED_QOS_1, $res['codes'][0]);
        $this->assertSame(ReasonCode::GRANTED_QOS_2, $res['codes'][1]);
    }

    /**
     * @depends testSubscribe
     */
    public function testPublish()
    {
        Coroutine::create(function () {
            $client = new WebSocketClient(SIMPS_MQTT_REMOTE_HOST, SIMPS_MQTT_OVER_WEBSOCKET_PORT, getTestMQTT5ConnectConfig());
            $res = $client->connect();
            $this->assertIsArray($res);
            $buffer = $client->publish(self::$topic . '/get', 'hello,simps', 1);
            $this->assertIsArray($buffer);
            $this->assertSame(Types::PUBACK, $buffer['type']);
            $this->assertSame('Success', ReasonCode::getReasonPhrase($buffer['code']));
        });
    }

    /**
     * @depends testSubscribe
     */
    public function testRecv()
    {
        $buffer = self::$client->recv();
        $this->assertIsArray($buffer);
        $this->assertSame(Types::PUBLISH, $buffer['type']);
        $this->assertSame(self::$topic . '/get', $buffer['topic']);
        $this->assertSame('hello,simps', $buffer['message']);
    }

    /**
     * @depends testRecv
     */
    public function testPing()
    {
        $buffer = self::$client->ping();
        $this->assertIsArray($buffer);
        $this->assertSame(Types::PINGRESP, $buffer['type']);
    }

    /**
     * @depends testPing
     */
    public function testUnsubscribe()
    {
        $status = self::$client->unSubscribe([self::$topic . '/get', self::$topic . '/update']);
        $this->assertIsArray($status);
        $this->assertSame(Types::UNSUBACK, $status['type']);
        $this->assertSame('Success', ReasonCode::getReasonPhrase($status['codes'][0]));
        $this->assertSame('Success', ReasonCode::getReasonPhrase($status['codes'][1]));
    }

    /**
     * @depends testUnsubscribe
     */
    public function testClose()
    {
        $status = self::$client->close();
        $this->assertTrue($status);
    }

    public function testPublishNonTopic()
    {
        $client = new WebSocketClient(SIMPS_MQTT_REMOTE_HOST, SIMPS_MQTT_OVER_WEBSOCKET_PORT, getTestMQTT5ConnectConfig());
        $client->connect();
        $this->expectException(ProtocolException::class);
        $this->expectExceptionMessage('Topic cannot be empty or need to set topic_alias');
        $client->publish('', 'hello,simps');
    }
}
