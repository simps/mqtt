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
        Property::SESSION_EXPIRY_INTERVAL => 'session_expiry_interval',
        Property::AUTHENTICATION_METHOD => 'authentication_method',
        Property::AUTHENTICATION_DATA => 'authentication_data',
        Property::REQUEST_PROBLEM_INFORMATION => 'request_problem_information',
        Property::REQUEST_RESPONSE_INFORMATION => 'request_response_information',
        Property::RECEIVE_MAXIMUM => 'receive_maximum',
        Property::TOPIC_ALIAS_MAXIMUM => 'topic_alias_maximum',
        Property::USER_PROPERTY => 'user_property',
        Property::MAXIMUM_PACKET_SIZE => 'maximum_packet_size',
    ];

    public static $connAck = [
        Property::SESSION_EXPIRY_INTERVAL => 'session_expiry_interval',
        Property::ASSIGNED_CLIENT_IDENTIFIER => 'assigned_client_identifier',
        Property::SERVER_KEEP_ALIVE => 'server_keep_alive',
        Property::AUTHENTICATION_METHOD => 'authentication_method',
        Property::AUTHENTICATION_DATA => 'authentication_data',
        Property::RESPONSE_INFORMATION => 'response_information',
        Property::SERVER_REFERENCE => 'server_reference',
        Property::REASON_STRING => 'reason_string',
        Property::RECEIVE_MAXIMUM => 'receive_maximum',
        Property::TOPIC_ALIAS_MAXIMUM => 'topic_alias_maximum',
        Property::MAXIMUM_QOS => 'maximum_qos',
        Property::RETAIN_AVAILABLE => 'retain_available',
        Property::USER_PROPERTY => 'user_property',
        Property::MAXIMUM_PACKET_SIZE => 'maximum_packet_size',
        Property::WILDCARD_SUBSCRIPTION_AVAILABLE => 'wildcard_subscription_available',
        Property::SUBSCRIPTION_IDENTIFIER_AVAILABLE => 'subscription_identifier_available',
        Property::SHARED_SUBSCRIPTION_AVAILABLE => 'shared_subscription_available',
    ];

    public static $publish = [
        Property::PAYLOAD_FORMAT_INDICATOR => 'payload_format_indicator',
        Property::MESSAGE_EXPIRY_INTERVAL => 'message_expiry_interval',
        Property::CONTENT_TYPE => 'content_type',
        Property::RESPONSE_TOPIC => 'response_topic',
        Property::CORRELATION_DATA => 'correlation_data',
        Property::SUBSCRIPTION_IDENTIFIER => 'subscription_identifier',
        Property::TOPIC_ALIAS => 'topic_alias',
        Property::USER_PROPERTY => 'user_property',
    ];

    /**
     * pubAck, pubRec, pubRel, pubComp, subAck, unSubAck
     */
    public static $pubAndSub = [
        Property::REASON_STRING => 'reason_string',
        Property::USER_PROPERTY => 'user_property',
    ];

    public static $subscribe = [
        Property::SUBSCRIPTION_IDENTIFIER => 'subscription_identifier',
        Property::USER_PROPERTY => 'user_property',
    ];

    public static $unSubscribe = [
        Property::USER_PROPERTY => 'user_property',
    ];

    public static $disConnect = [
        Property::SESSION_EXPIRY_INTERVAL => 'session_expiry_interval',
        Property::SERVER_REFERENCE => 'server_reference',
        Property::REASON_STRING => 'reason_string',
        Property::USER_PROPERTY => 'user_property',
    ];

    public static $auth = [
        Property::AUTHENTICATION_METHOD => 'authentication_method',
        Property::AUTHENTICATION_DATA => 'authentication_data',
        Property::REASON_STRING => 'reason_string',
        Property::USER_PROPERTY => 'user_property',
    ];

    public static $willProperties = [
        Property::PAYLOAD_FORMAT_INDICATOR => 'payload_format_indicator',
        Property::MESSAGE_EXPIRY_INTERVAL => 'message_expiry_interval',
        Property::CONTENT_TYPE => 'content_type',
        Property::RESPONSE_TOPIC => 'response_topic',
        Property::CORRELATION_DATA => 'correlation_data',
        Property::WILL_DELAY_INTERVAL => 'will_delay_interval',
        Property::USER_PROPERTY => 'user_property',
    ];
}
