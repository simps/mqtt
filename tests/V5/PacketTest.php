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

namespace SimpsTest\MQTT\V5;

use PHPUnit\Framework\TestCase;
use Simps\MQTT\Client;
use Simps\MQTT\Exception\ProtocolException;
use Simps\MQTT\Hex\ReasonCode;
use Simps\MQTT\Protocol\Types;

/**
 * @internal
 * @coversNothing
 */
class PacketTest extends TestCase
{
    private static $topic = '';

    private static $client;

    public static function setUpBeforeClass()
    {
        self::$topic = 'testtopic/simps-' . rand(100, 999);
        self::$client = new Client(SIMPS_MQTT_REMOTE_HOST, SIMPS_MQTT_PORT, getTestMQTT5ConnectConfig());
    }

    public static function tearDownAfterClass()
    {
        self::$topic = '';
        self::$client = null;
    }

    public function testConnect()
    {
        $res = self::$client->connect();
        $this->assertIsArray($res);
        $this->assertSame($res['type'], Types::CONNACK);
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
        $this->assertSame($res['type'], Types::SUBACK);
        $this->assertIsArray($res['codes']);
        $this->assertSame($res['codes'][0], ReasonCode::GRANTED_QOS_1);
        $this->assertSame($res['codes'][1], ReasonCode::GRANTED_QOS_2);
    }

    /**
     * @depends testSubscribe
     */
    public function testPublish()
    {
        go(function () {
            $client = new Client(SIMPS_MQTT_REMOTE_HOST, SIMPS_MQTT_PORT, getTestMQTT5ConnectConfig());
            $res = $client->connect();
            $this->assertIsArray($res);
            $buffer = $client->publish(self::$topic . '/get', 'hello,simps', 1);
            $this->assertIsArray($buffer);
            $this->assertSame($buffer['type'], Types::PUBACK);
            $this->assertSame(ReasonCode::getReasonPhrase($buffer['code']), 'Success');
        });
    }

    /**
     * @depends testSubscribe
     */
    public function testRecv()
    {
        $buffer = self::$client->recv();
        $this->assertIsArray($buffer);
        $this->assertSame($buffer['type'], Types::PUBLISH);
        $this->assertSame($buffer['topic'], self::$topic . '/get');
        $this->assertSame($buffer['message'], 'hello,simps');
    }

    /**
     * @depends testRecv
     */
    public function testPing()
    {
        $buffer = self::$client->ping();
        $this->assertIsArray($buffer);
        $this->assertSame($buffer['type'], Types::PINGRESP);
    }

    /**
     * @depends testPing
     */
    public function testUnsubscribe()
    {
        $status = self::$client->unSubscribe([self::$topic . '/get']);
        $this->assertIsArray($status);
        $this->assertSame($status['type'], Types::UNSUBACK);
        $this->assertSame(ReasonCode::getReasonPhrase($status['code']), 'Success');
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
        $client = new Client(SIMPS_MQTT_REMOTE_HOST, SIMPS_MQTT_PORT, getTestMQTT5ConnectConfig());
        $client->connect();
        $this->expectException(ProtocolException::class);
        $this->expectExceptionMessage('Protocol Error, Topic cannot be empty or need to set topic_alias');
        $client->publish('', 'hello,simps');

        return $client;
    }
}
