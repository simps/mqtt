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

namespace Simps\MQTT\Hex;

/**
 * @see https://docs.oasis-open.org/mqtt/mqtt/v5.0/os/mqtt-v5.0-os.html#_Toc3901029
 */
class Property
{
    public const PAYLOAD_FORMAT_INDICATOR = 0x01;

    public const MESSAGE_EXPIRY_INTERVAL = 0x02;

    public const CONTENT_TYPE = 0x03;

    public const RESPONSE_TOPIC = 0x08;

    public const CORRELATION_DATA = 0x09;

    public const SUBSCRIPTION_IDENTIFIER = 0x0B;

    public const SESSION_EXPIRY_INTERVAL = 0x11;

    public const ASSIGNED_CLIENT_IDENTIFIER = 0x12;

    public const SERVER_KEEP_ALIVE = 0x13;

    public const AUTHENTICATION_METHOD = 0x15;

    public const AUTHENTICATION_DATA = 0x16;

    public const REQUEST_PROBLEM_INFORMATION = 0x17;

    public const WILL_DELAY_INTERVAL = 0x18;

    public const REQUEST_RESPONSE_INFORMATION = 0x19;

    public const RESPONSE_INFORMATION = 0x1A;

    public const SERVER_REFERENCE = 0x1C;

    public const REASON_STRING = 0x1F;

    public const RECEIVE_MAXIMUM = 0x21;

    public const TOPIC_ALIAS_MAXIMUM = 0x22;

    public const TOPIC_ALIAS = 0x23;

    public const MAXIMUM_QOS = 0x24;

    public const RETAIN_AVAILABLE = 0x25;

    public const USER_PROPERTY = 0x26;

    public const MAXIMUM_PACKET_SIZE = 0x27;

    public const WILDCARD_SUBSCRIPTION_AVAILABLE = 0x28;

    public const SUBSCRIPTION_IDENTIFIER_AVAILABLE = 0x29;

    public const SHARED_SUBSCRIPTION_AVAILABLE = 0x2A;
}
