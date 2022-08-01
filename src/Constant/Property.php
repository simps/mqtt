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
namespace Simps\MQTT\Constant;

class Property
{
    public const PAYLOAD_FORMAT_INDICATOR = 'payload_format_indicator';

    public const MESSAGE_EXPIRY_INTERVAL = 'message_expiry_interval';

    public const CONTENT_TYPE = 'content_type';

    public const RESPONSE_TOPIC = 'response_topic';

    public const CORRELATION_DATA = 'correlation_data';

    public const SUBSCRIPTION_IDENTIFIER = 'subscription_identifier';

    public const SESSION_EXPIRY_INTERVAL = 'session_expiry_interval';

    public const ASSIGNED_CLIENT_IDENTIFIER = 'assigned_client_identifier';

    public const SERVER_KEEP_ALIVE = 'server_keep_alive';

    public const AUTHENTICATION_METHOD = 'authentication_method';

    public const AUTHENTICATION_DATA = 'authentication_data';

    public const REQUEST_PROBLEM_INFORMATION = 'request_problem_information';

    public const WILL_DELAY_INTERVAL = 'will_delay_interval';

    public const REQUEST_RESPONSE_INFORMATION = 'request_response_information';

    public const RESPONSE_INFORMATION = 'response_information';

    public const SERVER_REFERENCE = 'server_reference';

    public const REASON_STRING = 'reason_string';

    public const RECEIVE_MAXIMUM = 'receive_maximum';

    public const TOPIC_ALIAS_MAXIMUM = 'topic_alias_maximum';

    public const TOPIC_ALIAS = 'topic_alias';

    public const MAXIMUM_QOS = 'maximum_qos';

    public const RETAIN_AVAILABLE = 'retain_available';

    public const USER_PROPERTY = 'user_property';

    public const MAXIMUM_PACKET_SIZE = 'maximum_packet_size';

    public const WILDCARD_SUBSCRIPTION_AVAILABLE = 'wildcard_subscription_available';

    public const SUBSCRIPTION_IDENTIFIER_AVAILABLE = 'subscription_identifier_available';

    public const SHARED_SUBSCRIPTION_AVAILABLE = 'shared_subscription_available';
}
