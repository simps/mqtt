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
namespace SimpsTest\MQTT\Unit;

use PHPUnit\Framework\TestCase;
use Simps\MQTT\Message;
use Simps\MQTT\Protocol\ProtocolInterface;
use Simps\MQTT\Protocol\Types;

/**
 * @internal
 * @coversNothing
 */
class MessageTest extends TestCase
{
    public function testPublishMessage()
    {
        $message = new Message\Publish();
        $message->setProtocolLevel(ProtocolInterface::MQTT_PROTOCOL_LEVEL_5_0)
            ->setTopic('simps/mqtt/message')
            ->setQos(ProtocolInterface::MQTT_QOS_1)
            ->setDup(ProtocolInterface::MQTT_DUP_0)
            ->setRetain(ProtocolInterface::MQTT_RETAIN_0)
            ->setMessage('this is content')
            ->setMessageId(1)
            ->setProperties(['message_expiry_interval' => 100]);
        $this->assertEquals(
            (string) $message,
            $message->getContents(),
            'The results of getContents and toString should be the same'
        );
        $this->assertIsArray($message->getContents(true));
        $this->assertIsArray($message->toArray());
        $this->assertEquals(
            $message->toArray(),
            $message->getContents(true),
            'The results of getContents and toArray should be the same'
        );
    }

    public function testPingRespMessage()
    {
        $message = new Message\PingResp();
        $this->assertEquals(
            $message->getContents(),
            (string) $message,
            'The results of getContents and toString should be the same'
        );
        $this->assertIsArray($message->getContents(true));
        $this->assertEquals(Types::PINGRESP, $message->getContents(true)['type']);
        $this->assertIsArray($message->toArray());
        $this->assertEquals(
            $message->toArray(),
            $message->getContents(true),
            'The results of getContents and toArray should be the same'
        );
    }

    public function testWillMessage()
    {
        $message = new Message\Will();
        $message->setTopic('topic')
            ->setQos(ProtocolInterface::MQTT_QOS_1)
            ->setRetain(ProtocolInterface::MQTT_RETAIN_0)
            ->setMessage('this is content');
        $this->assertIsArray($message->getContents(true));
        $this->assertIsArray($message->toArray());
        $this->assertEquals(
            $message->toArray(),
            $message->getContents(true),
            'The results of getContents and toArray should be the same'
        );
    }

    public function testIsMQTT5()
    {
        $message = new Message\Publish();
        $message->setTopic('simps/mqtt/message')
            ->setQos(ProtocolInterface::MQTT_QOS_1)
            ->setDup(ProtocolInterface::MQTT_DUP_0)
            ->setRetain(ProtocolInterface::MQTT_RETAIN_0)
            ->setMessage('this is content')
            ->setMessageId(1);

        $this->assertFalse($message->isMQTT5());
        $this->assertEquals(ProtocolInterface::MQTT_PROTOCOL_LEVEL_3_1_1, $message->getProtocolLevel());

        $message->setProperties(['message_expiry_interval' => 100]);
        $this->assertTrue($message->isMQTT5());
        $this->assertEquals(ProtocolInterface::MQTT_PROTOCOL_LEVEL_5_0, $message->getProtocolLevel());

        $message->setProtocolLevel(ProtocolInterface::MQTT_PROTOCOL_LEVEL_3_1);
        $this->assertTrue($message->isMQTT5());
        $this->assertEquals(ProtocolInterface::MQTT_PROTOCOL_LEVEL_5_0, $message->getProtocolLevel());
    }
}
