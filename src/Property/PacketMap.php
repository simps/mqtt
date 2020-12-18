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

namespace Simps\MQTT\Property;

use Simps\MQTT\Hex\Property;

/**
 * There is no significance in the order of Properties with different Identifiers
 * @see https://docs.oasis-open.org/mqtt/mqtt/v5.0/os/mqtt-v5.0-os.html#_Toc3901029
 */
class PacketMap
{
    public static $connect = [
        Property::SESSION_EXPIRY_INTERVAL,
        Property::AUTHENTICATION_METHOD,
        Property::AUTHENTICATION_DATA,
        Property::REQUEST_PROBLEM_INFORMATION,
        Property::REQUEST_RESPONSE_INFORMATION,
        Property::RECEIVE_MAXIMUM,
        Property::TOPIC_ALIAS_MAXIMUM,
        Property::USER_PROPERTY,
        Property::MAXIMUM_PACKET_SIZE,
    ];

    public static $connAck = [
        Property::SESSION_EXPIRY_INTERVAL,
        Property::ASSIGNED_CLIENT_IDENTIFIER,
        Property::SERVER_KEEP_ALIVE,
        Property::AUTHENTICATION_METHOD,
        Property::AUTHENTICATION_DATA,
        Property::RESPONSE_INFORMATION,
        Property::SERVER_REFERENCE,
        Property::REASON_STRING,
        Property::RECEIVE_MAXIMUM,
        Property::TOPIC_ALIAS_MAXIMUM,
        Property::MAXIMUM_QOS,
        Property::RETAIN_AVAILABLE,
        Property::USER_PROPERTY,
        Property::MAXIMUM_PACKET_SIZE,
        Property::WILDCARD_SUBSCRIPTION_AVAILABLE,
        Property::SUBSCRIPTION_IDENTIFIER_AVAILABLE,
        Property::SHARED_SUBSCRIPTION_AVAILABLE,
    ];

    public static $publish = [
        Property::PAYLOAD_FORMAT_INDICATOR,
        Property::MESSAGE_EXPIRY_INTERVAL,
        Property::CONTENT_TYPE,
        Property::RESPONSE_TOPIC,
        Property::CORRELATION_DATA,
        Property::SUBSCRIPTION_IDENTIFIER,
        Property::TOPIC_ALIAS,
        Property::USER_PROPERTY,
    ];

    /**
     * pubAck, pubRec, pubRel, pubComp, subAck, unSubAck
     */
    public static $pubAndSub = [
        Property::REASON_STRING,
        Property::USER_PROPERTY,
    ];

    public static $subscribe = [
        Property::SUBSCRIPTION_IDENTIFIER,
        Property::USER_PROPERTY,
    ];

    public static $unSubscribe = [
        Property::USER_PROPERTY,
    ];

    public static $disConnect = [
        Property::SESSION_EXPIRY_INTERVAL,
        Property::SERVER_REFERENCE,
        Property::REASON_STRING,
        Property::USER_PROPERTY,
    ];

    public static $auth = [
        Property::AUTHENTICATION_METHOD,
        Property::AUTHENTICATION_DATA,
        Property::REASON_STRING,
        Property::USER_PROPERTY,
    ];

    public static $willProperties = [
        Property::PAYLOAD_FORMAT_INDICATOR,
        Property::MESSAGE_EXPIRY_INTERVAL,
        Property::CONTENT_TYPE,
        Property::RESPONSE_TOPIC,
        Property::CORRELATION_DATA,
        Property::WILL_DELAY_INTERVAL,
        Property::USER_PROPERTY,
    ];
}
