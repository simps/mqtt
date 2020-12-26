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

namespace Simps\MQTT;

interface ProtocolInterface
{
    const MQTT_PROTOCOL_LEVEL_3_1 = 3;

    const MQTT_PROTOCOL_LEVEL_3_1_1 = 4;

    const MQTT_PROTOCOL_LEVEL_5_0 = 5;

    const MQISDP_PROTOCOL_NAME = 'MQIsdp';

    const MQTT_PROTOCOL_NAME = 'MQTT';

    public static function pack(array $array): string;

    public static function unpack(string $data): array;
}
