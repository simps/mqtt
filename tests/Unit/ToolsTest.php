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
use Simps\MQTT\Exception\InvalidArgumentException;
use Simps\MQTT\Protocol\ProtocolInterface;
use Simps\MQTT\Tools\UnPackTool;

/**
 * @internal
 * @coversNothing
 */
class ToolsTest extends TestCase
{
    public function testGetLevel()
    {
        $connect_31 = '104400064d51497364700306000a001353696d70735f36313362316164323236626334001973696d70732d6d7174742f757365723030312f64656c6574650006627965627965';
        $connect_311 = '104200044d5154540406000a001353696d70735f36313362313830323035636633001973696d70732d6d7174742f757365723030312f64656c6574650006627965627965';
        $connect_50 = '106200044d515454050e000a0b110000003c21ffff22ffff001353696d70735f3631336231613330313962663213180000003c020000003c030004746573740101001973696d70732d6d7174742f757365723030312f64656c6574650006627965627965';
        $this->assertSame(UnPackTool::getLevel(hex2bin($connect_31)), ProtocolInterface::MQTT_PROTOCOL_LEVEL_3_1);
        $this->assertSame(UnPackTool::getLevel(hex2bin($connect_311)), ProtocolInterface::MQTT_PROTOCOL_LEVEL_3_1_1);
        $this->assertSame(UnPackTool::getLevel(hex2bin($connect_50)), ProtocolInterface::MQTT_PROTOCOL_LEVEL_5_0);
    }

    public function testInvalidGetLevel()
    {
        try {
            $connAck = '200200';
            UnPackTool::getLevel(hex2bin($connAck));
        } catch (\Throwable $ex) {
            $this->assertInstanceOf(InvalidArgumentException::class, $ex);
            $this->assertSame($ex->getMessage(), 'packet must be of type connect, connack given');
        }
    }
}
