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
 * @see https://docs.oasis-open.org/mqtt/mqtt/v5.0/os/mqtt-v5.0-os.html#_Toc3901031
 */
abstract class ReasonCode
{
    const SUCCESS = 0x00;

    const NORMAL_DISCONNECTION = 0x00;

    const GRANTED_QOS_0 = 0x00;

    const GRANTED_QOS_1 = 0x01;

    const GRANTED_QOS_2 = 0x02;

    const DISCONNECT_WITH_WILL_MESSAGE = 0x04;

    const NO_MATCHING_SUBSCRIBERS = 0x10;

    const NO_SUBSCRIPTION_EXISTED = 0x11;

    const CONTINUE_AUTHENTICATION = 0x18;

    const RE_AUTHENTICATE = 0x19;

    const UNSPECIFIED_ERROR = 0x80;

    const MALFORMED_PACKET = 0x81;

    const PROTOCOL_ERROR = 0x82;

    const IMPLEMENTATION_SPECIFIC_ERROR = 0x83;

    const UNSUPPORTED_PROTOCOL_VERSION = 0x84;

    const CLIENT_IDENTIFIER_NOT_VALID = 0x85;

    const BAD_USER_NAME_OR_PASSWORD = 0x86;

    const NOT_AUTHORIZED = 0x87;

    const SERVER_UNAVAILABLE = 0x88;

    const SERVER_BUSY = 0x89;

    const BANNED = 0x8A;

    const SERVER_SHUTTING_DOWN = 0x8B;

    const BAD_AUTHENTICATION_METHOD = 0x8C;

    const KEEP_ALIVE_TIMEOUT = 0x8D;

    const SESSION_TAKEN_OVER = 0x8E;

    const TOPIC_FILTER_INVALID = 0x8F;

    const TOPIC_NAME_INVALID = 0x90;

    const PACKET_IDENTIFIER_IN_USE = 0x91;

    const PACKET_IDENTIFIER_NOT_FOUND = 0x92;

    const RECEIVE_MAXIMUM_EXCEEDED = 0x93;

    const TOPIC_ALIAS_INVALID = 0x94;

    const PACKET_TOO_LARGE = 0x95;

    const MESSAGE_RATE_TOO_HIGH = 0x96;

    const QUOTA_EXCEEDED = 0x97;

    const ADMINISTRATIVE_ACTION = 0x98;

    const PAYLOAD_FORMAT_INVALID = 0x99;

    const RETAIN_NOT_SUPPORTED = 0x9A;

    const QOS_NOT_SUPPORTED = 0x9B;

    const USE_ANOTHER_SERVER = 0x9C;

    const SERVER_MOVED = 0x9D;

    const SHARED_SUBSCRIPTIONS_NOT_SUPPORTED = 0x9E;

    const CONNECTION_RATE_EXCEEDED = 0x9F;

    const MAXIMUM_CONNECT_TIME = 0xA0;

    const SUBSCRIPTION_IDENTIFIERS_NOT_SUPPORTED = 0xA1;

    const WILDCARD_SUBSCRIPTIONS_NOT_SUPPORTED = 0xA2;

    /**
     * @see https://docs.oasis-open.org/mqtt/mqtt/v5.0/os/mqtt-v5.0-os.html#_Toc3901079
     */
    protected static $reasonPhrases = [
        self::SUCCESS => 'Success',
        self::UNSPECIFIED_ERROR => 'Unspecified error',
        self::MALFORMED_PACKET => 'Malformed Packet',
        self::PROTOCOL_ERROR => 'Protocol Error',
        self::IMPLEMENTATION_SPECIFIC_ERROR => 'Implementation specific error',
        self::UNSUPPORTED_PROTOCOL_VERSION => 'Unsupported Protocol Version',
        self::CLIENT_IDENTIFIER_NOT_VALID => 'Client Identifier not valid',
        self::BAD_USER_NAME_OR_PASSWORD => 'Bad User Name or Password',
        self::NOT_AUTHORIZED => 'Not authorized',
        self::SERVER_UNAVAILABLE => 'Server unavailable',
        self::SERVER_BUSY => 'Server busy',
        self::BANNED => 'Banned',
        self::BAD_AUTHENTICATION_METHOD => 'Bad authentication method',
        self::TOPIC_NAME_INVALID => 'Topic Name invalid',
        self::PACKET_TOO_LARGE => 'Packet too large',
        self::QUOTA_EXCEEDED => 'Quota exceeded',
        self::PAYLOAD_FORMAT_INVALID => 'Payload format invalid',
        self::RETAIN_NOT_SUPPORTED => 'Retain not supported',
        self::QOS_NOT_SUPPORTED => 'QoS not supported',
        self::USE_ANOTHER_SERVER => 'Use another server',
        self::SERVER_MOVED => 'Server moved',
        self::CONNECTION_RATE_EXCEEDED => 'Connection rate exceeded',
    ];

    public static function getReasonPhrases(): array
    {
        return static::$reasonPhrases;
    }

    public static function getReasonPhrase(int $value): string
    {
        return static::$reasonPhrases[$value] ?? 'Unknown';
    }
}
