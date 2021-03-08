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
use Simps\MQTT\Client;
use Simps\MQTT\Config\ClientConfig;
use Simps\MQTT\Protocol\ProtocolInterface;

/**
 * @internal
 * @coversNothing
 */
class ClientConfigTest extends TestCase
{
    public function testIsMQTT5()
    {
        $config = new ClientConfig();
        $config->setClientId(Client::genClientID())
            ->setKeepAlive(10)
            ->setDelay(3000)
            ->setMaxAttempts(5)
            ->setSwooleConfig(SWOOLE_MQTT_CONFIG);
        $this->assertEquals($config->getUserName(), '');
        $this->assertEquals($config->getPassword(), '');
        $this->assertFalse($config->isMQTT5());
        $this->assertEquals($config->getProtocolLevel(), ProtocolInterface::MQTT_PROTOCOL_LEVEL_3_1_1);

        $config->setProperties(['receive_maximum' => 65535]);
        $this->assertTrue($config->isMQTT5());
        $this->assertEquals($config->getProtocolLevel(), ProtocolInterface::MQTT_PROTOCOL_LEVEL_5_0);

        $config->setProtocolLevel(ProtocolInterface::MQTT_PROTOCOL_LEVEL_3_1);
        $this->assertTrue($config->isMQTT5());
        $this->assertEquals($config->getProtocolLevel(), ProtocolInterface::MQTT_PROTOCOL_LEVEL_5_0);
    }
}
