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
            ->setDup(1)
            ->setRetain(0)
            ->setMessage('this is content')
            ->setMessageId(1)
            ->setProperties(['message_expiry_interval' => 100]);
        $this->assertEquals(
            $message->getContents(),
            (string) $message,
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
        $this->assertEquals($message->getContents(true)['type'], Types::PINGRESP);
        $this->assertIsArray($message->toArray());
        $this->assertEquals(
            $message->toArray(),
            $message->getContents(true),
            'The results of getContents and toArray should be the same'
        );
    }
}
