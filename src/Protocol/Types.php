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

namespace Simps\MQTT\Protocol;

class Types
{
    public const CONNECT = 1; // Client request to connect to Server

    public const CONNACK = 2; // Connect acknowledgment

    public const PUBLISH = 3; // Publish message

    public const PUBACK = 4; // Publish acknowledgment

    public const PUBREC = 5; // Publish received (assured delivery part 1)

    public const PUBREL = 6; // Publish release (assured delivery part 2)

    public const PUBCOMP = 7; // Publish complete (assured delivery part 3)

    public const SUBSCRIBE = 8; // Client subscribe request

    public const SUBACK = 9; // Subscribe acknowledgment

    public const UNSUBSCRIBE = 10; // Unsubscribe request

    public const UNSUBACK = 11; // Unsubscribe acknowledgment

    public const PINGREQ = 12; // PING request

    public const PINGRESP = 13; // PING response

    public const DISCONNECT = 14; // Client is disconnecting

    public const AUTH = 15; // Authentication exchange
}
