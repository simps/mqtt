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
namespace Simps\MQTT\Hex;

/**
 * @see https://docs.oasis-open.org/mqtt/mqtt/v5.0/os/mqtt-v5.0-os.html#_Toc3901031
 */
abstract class ReasonCode
{
    public const SUCCESS = 0x00;

    public const NORMAL_DISCONNECTION = 0x00;

    public const GRANTED_QOS_0 = 0x00;

    public const GRANTED_QOS_1 = 0x01;

    public const GRANTED_QOS_2 = 0x02;

    public const DISCONNECT_WITH_WILL_MESSAGE = 0x04;

    public const NO_MATCHING_SUBSCRIBERS = 0x10;

    public const NO_SUBSCRIPTION_EXISTED = 0x11;

    public const CONTINUE_AUTHENTICATION = 0x18;

    public const RE_AUTHENTICATE = 0x19;

    public const UNSPECIFIED_ERROR = 0x80;

    public const MALFORMED_PACKET = 0x81;

    public const PROTOCOL_ERROR = 0x82;

    public const IMPLEMENTATION_SPECIFIC_ERROR = 0x83;

    public const UNSUPPORTED_PROTOCOL_VERSION = 0x84;

    public const CLIENT_IDENTIFIER_NOT_VALID = 0x85;

    public const BAD_USER_NAME_OR_PASSWORD = 0x86;

    public const NOT_AUTHORIZED = 0x87;

    public const SERVER_UNAVAILABLE = 0x88;

    public const SERVER_BUSY = 0x89;

    public const BANNED = 0x8A;

    public const SERVER_SHUTTING_DOWN = 0x8B;

    public const BAD_AUTHENTICATION_METHOD = 0x8C;

    public const KEEP_ALIVE_TIMEOUT = 0x8D;

    public const SESSION_TAKEN_OVER = 0x8E;

    public const TOPIC_FILTER_INVALID = 0x8F;

    public const TOPIC_NAME_INVALID = 0x90;

    public const PACKET_IDENTIFIER_IN_USE = 0x91;

    public const PACKET_IDENTIFIER_NOT_FOUND = 0x92;

    public const RECEIVE_MAXIMUM_EXCEEDED = 0x93;

    public const TOPIC_ALIAS_INVALID = 0x94;

    public const PACKET_TOO_LARGE = 0x95;

    public const MESSAGE_RATE_TOO_HIGH = 0x96;

    public const QUOTA_EXCEEDED = 0x97;

    public const ADMINISTRATIVE_ACTION = 0x98;

    public const PAYLOAD_FORMAT_INVALID = 0x99;

    public const RETAIN_NOT_SUPPORTED = 0x9A;

    public const QOS_NOT_SUPPORTED = 0x9B;

    public const USE_ANOTHER_SERVER = 0x9C;

    public const SERVER_MOVED = 0x9D;

    public const SHARED_SUBSCRIPTIONS_NOT_SUPPORTED = 0x9E;

    public const CONNECTION_RATE_EXCEEDED = 0x9F;

    public const MAXIMUM_CONNECT_TIME = 0xA0;

    public const SUBSCRIPTION_IDENTIFIERS_NOT_SUPPORTED = 0xA1;

    public const WILDCARD_SUBSCRIPTIONS_NOT_SUPPORTED = 0xA2;

    /**
     * @var array
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
        self::DISCONNECT_WITH_WILL_MESSAGE => 'Disconnect with Will Message',
        self::SERVER_SHUTTING_DOWN => 'Server shutting down',
        self::KEEP_ALIVE_TIMEOUT => 'Keep Alive timeout',
        self::SESSION_TAKEN_OVER => 'Session taken over',
        self::TOPIC_FILTER_INVALID => 'Topic Filter invalid',
        self::RECEIVE_MAXIMUM_EXCEEDED => 'Receive Maximum exceeded',
        self::TOPIC_ALIAS_INVALID => 'Topic Alias invalid',
        self::MESSAGE_RATE_TOO_HIGH => 'Message rate too high',
        self::ADMINISTRATIVE_ACTION => 'Administrative action',
        self::SHARED_SUBSCRIPTIONS_NOT_SUPPORTED => 'Shared Subscriptions not supported',
        self::MAXIMUM_CONNECT_TIME => 'Maximum connect time',
        self::SUBSCRIPTION_IDENTIFIERS_NOT_SUPPORTED => 'Subscription Identifiers not supported',
        self::WILDCARD_SUBSCRIPTIONS_NOT_SUPPORTED => 'Wildcard Subscriptions not supported',
        self::NO_MATCHING_SUBSCRIBERS => 'No matching subscribers',
        self::NO_SUBSCRIPTION_EXISTED => 'No subscription existed',
        self::CONTINUE_AUTHENTICATION => 'Continue authentication',
        self::RE_AUTHENTICATE => 'Re-authenticate',
        self::PACKET_IDENTIFIER_IN_USE => 'Packet Identifier in use',
        self::PACKET_IDENTIFIER_NOT_FOUND => 'Packet Identifier not found',
    ];

    /** @var array */
    protected static $qosReasonPhrases = [
        self::GRANTED_QOS_0 => 'Granted QoS 0',
        self::GRANTED_QOS_1 => 'Granted QoS 1',
        self::GRANTED_QOS_2 => 'Granted QoS 2',
    ];

    public static function getReasonPhrases(bool $isQos = false): array
    {
        if ($isQos) {
            return static::$qosReasonPhrases;
        }

        return static::$reasonPhrases;
    }

    public static function getReasonPhrase(int $value, bool $isQos = false): string
    {
        if ($isQos) {
            return static::$qosReasonPhrases[$value] ?? 'QoS not supported';
        }

        return static::$reasonPhrases[$value] ?? 'Unknown';
    }
}
