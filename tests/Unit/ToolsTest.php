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
use Simps\MQTT\Exception\InvalidArgumentException;
use Simps\MQTT\Protocol\ProtocolInterface;
use Simps\MQTT\Tools\Debug;
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
        $this->assertSame(ProtocolInterface::MQTT_PROTOCOL_LEVEL_3_1, UnPackTool::getLevel(hex2bin($connect_31)));
        $this->assertSame(ProtocolInterface::MQTT_PROTOCOL_LEVEL_3_1_1, UnPackTool::getLevel(hex2bin($connect_311)));
        $this->assertSame(ProtocolInterface::MQTT_PROTOCOL_LEVEL_5_0, UnPackTool::getLevel(hex2bin($connect_50)));
    }

    public function testInvalidGetLevel()
    {
        try {
            $connAck = '200200';
            UnPackTool::getLevel(hex2bin($connAck));
        } catch (\Throwable $ex) {
            $this->assertInstanceOf(InvalidArgumentException::class, $ex);
            $this->assertSame('packet must be of type connect, connack given', $ex->getMessage());
        }
    }

    public function testDebug2HexDump()
    {
        $hex = '106200044d515454050e000a0b110000003c21ffff22ffff001353696d70735f3631336231613330313962663213180000003c020000003c030004746573740101001973696d70732d6d7174742f757365723030312f64656c6574650006627965627965';
        $string = '00000000    10 62 00 04 4d 51 54 54 05 0e 00 0a 0b 11 00 00 
00000010    00 3c 21 ff ff 22 ff ff 00 13 53 69 6d 70 73 5f 
00000020    36 31 33 62 31 61 33 30 31 39 62 66 32 13 18 00 
00000030    00 00 3c 02 00 00 00 3c 03 00 04 74 65 73 74 01 
00000040    01 00 19 73 69 6d 70 73 2d 6d 71 74 74 2f 75 73 
00000050    65 72 30 30 31 2f 64 65 6c 65 74 65 00 06 62 79 
00000060    65 62 79 65                                     ';
        $bin = hex2bin($hex);
        $this->assertSame($string, (new Debug($bin))->hexDump());
        $this->assertSame($string, UnPackTool::hexDump($bin));
    }

    public function testDebug2hexDumpAscii()
    {
        $hex = '106200044d515454050e000a0b110000003c21ffff22ffff001353696d70735f3631336231613330313962663213180000003c020000003c030004746573740101001973696d70732d6d7174742f757365723030312f64656c6574650006627965627965';
        $string = '00000000    10 62 00 04 4d 51 54 54 05 0e 00 0a 0b 11 00 00    .b..MQTT........
00000010    00 3c 21 ff ff 22 ff ff 00 13 53 69 6d 70 73 5f    .<!.."....Simps_
00000020    36 31 33 62 31 61 33 30 31 39 62 66 32 13 18 00    613b1a3019bf2...
00000030    00 00 3c 02 00 00 00 3c 03 00 04 74 65 73 74 01    ..<....<...test.
00000040    01 00 19 73 69 6d 70 73 2d 6d 71 74 74 2f 75 73    ...simps-mqtt/us
00000050    65 72 30 30 31 2f 64 65 6c 65 74 65 00 06 62 79    er001/delete..by
00000060    65 62 79 65                                        ebye';
        $bin = hex2bin($hex);
        $this->assertSame($string, (new Debug($bin))->hexDumpAscii());
        $this->assertSame($string, UnPackTool::hexDumpAscii($bin));
    }

    public function testDebug2PrintableText()
    {
        $hex = '106200044d515454050e000a0b110000003c21ffff22ffff001353696d70735f3631336231613330313962663213180000003c020000003c030004746573740101001973696d70732d6d7174742f757365723030312f64656c6574650006627965627965';
        $bin = hex2bin($hex);
        $this->assertSame($bin, (new Debug($bin))->printableText());
        $this->assertSame($bin, UnPackTool::printableText($bin));
    }

    public function testDebug2HexStream()
    {
        $hex = '106200044d515454050e000a0b110000003c21ffff22ffff001353696d70735f3631336231613330313962663213180000003c020000003c030004746573740101001973696d70732d6d7174742f757365723030312f64656c6574650006627965627965';
        $bin = hex2bin($hex);
        $this->assertSame($hex, (new Debug($bin))->hexStream());
        $this->assertSame($hex, UnPackTool::hexStream($bin));
    }

    public function testDebug2Ascii()
    {
        $hex = '106200044d515454050e000a0b110000003c21ffff22ffff001353696d70735f3631336231613330313962663213180000003c020000003c030004746573740101001973696d70732d6d7174742f757365723030312f64656c6574650006627965627965';
        $string = '00000000    .b..MQTT........
00000010    .<!.."....Simps_
00000020    613b1a3019bf2...
00000030    ..<....<...test.
00000040    ...simps-mqtt/us
00000050    er001/delete..by
00000060    ebye';
        $bin = hex2bin($hex);
        $this->assertSame($string, (new Debug($bin))->ascii());
        $this->assertSame($string, UnPackTool::ascii($bin));
    }
}
