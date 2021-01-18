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

    public static function setUpBeforeClass()
    {
        self::$topic = 'testtopic/simps-' . rand(100, 999);
        self::$client = new Client(SIMPS_MQTT_REMOTE_HOST, SIMPS_MQTT_PORT, getTestConnectConfig());
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
        $topics[self::$topic] = 1;
        $res = self::$client->subscribe($topics);
        $this->assertIsArray($res);
        $this->assertSame($res['type'], Types::SUBACK);
        $this->assertSame($res['codes'][0], ReasonCode::GRANTED_QOS_1);
    }

    /**
     * @depends testSubscribe
     */
    public function testPublish()
    {
        $buffer = self::$client->publish(self::$topic, 'hello,simps', 1);
        $this->assertIsArray($buffer);
        $this->assertSame($buffer['type'], Types::PUBACK);
    }

    /**
     * @depends testPublish
     */
    public function testRecv()
    {
        $buffer = self::$client->recv();
        $this->assertIsArray($buffer);
        $this->assertSame($buffer['type'], Types::PUBLISH);
        $this->assertSame($buffer['topic'], self::$topic);
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
        $status = self::$client->unSubscribe([self::$topic]);
        $this->assertIsArray($status);
        $this->assertSame($status['type'], Types::UNSUBACK);
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
        $this->expectExceptionMessage('Protocol Error, Topic cannot be empty');
        $client->publish('', 'hello,simps');

        return $client;
    }
}
