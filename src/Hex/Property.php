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
    const PAYLOAD_FORMAT_INDICATOR = 0x01;

    const MESSAGE_EXPIRY_INTERVAL = 0x02;

    const CONTENT_TYPE = 0x03;

    const RESPONSE_TOPIC = 0x08;

    const CORRELATION_DATA = 0x09;

    const SUBSCRIPTION_IDENTIFIER = 0x0B;

    const SESSION_EXPIRY_INTERVAL = 0x11;

    const ASSIGNED_CLIENT_IDENTIFIER = 0x12;

    const SERVER_KEEP_ALIVE = 0x13;

    const AUTHENTICATION_METHOD = 0x15;

    const AUTHENTICATION_DATA = 0x16;

    const REQUEST_PROBLEM_INFORMATION = 0x17;

    const WILL_DELAY_INTERVAL = 0x18;

    const REQUEST_RESPONSE_INFORMATION = 0x19;

    const RESPONSE_INFORMATION = 0x1A;

    const SERVER_REFERENCE = 0x1C;

    const REASON_STRING = 0x1F;

    const RECEIVE_MAXIMUM = 0x21;

    const TOPIC_ALIAS_MAXIMUM = 0x22;

    const TOPIC_ALIAS = 0x23;

    const MAXIMUM_QOS = 0x24;

    const RETAIN_AVAILABLE = 0x25;

    const USER_PROPERTY = 0x26;

    const MAXIMUM_PACKET_SIZE = 0x27;

    const WILDCARD_SUBSCRIPTION_AVAILABLE = 0x28;

    const SUBSCRIPTION_IDENTIFIER_AVAILABLE = 0x29;

    const SHARED_SUBSCRIPTION_AVAILABLE = 0x2A;
}
