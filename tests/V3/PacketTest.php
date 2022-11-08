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
namespace SimpsTest\MQTT\V3;

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

    public static function setUpBeforeClass(): void
    {
        self::$topic = 'testtopic/simps-' . rand(100, 999);
        self::$client = new Client(SIMPS_MQTT_REMOTE_HOST, SIMPS_MQTT_PORT, getTestConnectConfig());
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
        $topics[self::$topic] = 1;
        $res = self::$client->subscribe($topics);
        $this->assertIsArray($res);
        $this->assertSame(Types::SUBACK, $res['type']);
        $this->assertSame(ReasonCode::GRANTED_QOS_1, $res['codes'][0]);
    }

    /**
     * @depends testSubscribe
     */
    public function testPublish()
    {
        $buffer = self::$client->publish(self::$topic, 'hello,simps', 1);
        $this->assertIsArray($buffer);
        $this->assertSame(Types::PUBACK, $buffer['type']);
    }

    /**
     * @depends testPublish
     */
    public function testRecv()
    {
        $buffer = self::$client->recv();
        $this->assertIsArray($buffer);
        $this->assertSame(Types::PUBLISH, $buffer['type']);
        $this->assertSame(self::$topic, $buffer['topic']);
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
        $status = self::$client->unSubscribe([self::$topic]);
        $this->assertIsArray($status);
        $this->assertSame(Types::UNSUBACK, $status['type']);
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
        $client = new Client(SIMPS_MQTT_REMOTE_HOST, SIMPS_MQTT_PORT, getTestConnectConfig());
        $client->connect();
        $this->expectException(ProtocolException::class);
        $this->expectExceptionMessage('Topic cannot be empty');
        $client->publish('', 'hello,simps');
    }
}
