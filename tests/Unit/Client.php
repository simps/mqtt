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
use Simps\MQTT\Client as MQTTClient;
use Simps\MQTT\Exception\ConnectException;

/**
 * @internal
 * @coversNothing
 */
class Client extends TestCase
{
    public function testConnectException()
    {
        try {
            $client = new MQTTClient(SIMPS_MQTT_LOCAL_HOST, SIMPS_MQTT_PORT, getTestConnectConfig());
            $client->connect();
        } catch (\Throwable $ex) {
            $this->assertInstanceOf(ConnectException::class, $ex);
        }
    }
}
