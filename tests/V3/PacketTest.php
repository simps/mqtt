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
use Simps\MQTT\Hex\ReasonCode;
use Simps\MQTT\Types;

/**
 * @internal
 * @coversNothing
 */
class PacketTest extends TestCase
{
    private static $topic = '';

    public static function setUpBeforeClass()
    {
        self::$topic = 'testtopic/simps-' . rand(100, 999);
    }

    public static function tearDownAfterClass()
    {
        self::$topic = '';
    }

    public function testConnect()
    {
        $config = getTestConnectConfig('broker.emqx.io');
        $client = new Client($config, SWOOLE_MQTT_CONFIG);
        $res = $client->connect();
        $this->assertIsArray($res);
        $this->assertSame($res['type'], Types::CONNACK);

        return $client;
    }

    /**
     * @depends testConnect
     */
    public function testSubscribe(Client $client)
    {
        $topics[self::$topic] = 1;
        $res = $client->subscribe($topics);
        $this->assertIsArray($res);
        $this->assertSame($res['type'], Types::SUBACK);
        $this->assertSame($res['codes'][0], ReasonCode::GRANTED_QOS_1);

        return $client;
    }

    /**
     * @depends testSubscribe
     */
    public function testPublish(Client $client)
    {
        $buffer = $client->publish(self::$topic, 'hello,simps', 1);
        $this->assertIsArray($buffer);
        $this->assertSame($buffer['type'], Types::PUBACK);

        return $client;
    }

    /**
     * @depends testPublish
     */
    public function testRecv(Client $client)
    {
        $buffer = $client->recv();
        $this->assertIsArray($buffer);
        $this->assertSame($buffer['type'], Types::PUBLISH);
        $this->assertSame($buffer['topic'], self::$topic);
        $this->assertSame($buffer['message'], 'hello,simps');

        return $client;
    }

    /**
     * @depends testRecv
     */
    public function testPing(Client $client)
    {
        $buffer = $client->ping();
        $this->assertIsArray($buffer);
        $this->assertSame($buffer['type'], Types::PINGRESP);

        return $client;
    }

    /**
     * @depends testPing
     */
    public function testUnsubscribe(Client $client)
    {
        $status = $client->unSubscribe([self::$topic]);
        $this->assertIsArray($status);
        $this->assertSame($status['type'], Types::UNSUBACK);

        return $client;
    }

    /**
     * @depends testUnsubscribe
     */
    public function testClose(Client $client)
    {
        $status = $client->close();
        $this->assertTrue($status);
    }
}
