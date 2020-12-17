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

namespace Simps\MQTT\Packet;

use Simps\MQTT\Hex\Property;
use Simps\MQTT\Hex\ReasonCode;
use Simps\MQTT\Tools\PackTool;
use Simps\MQTT\Types;

class PackV5
{
    public static function connect(array $array): string
    {
        $body = PackTool::string($array['protocol_name']) . chr($array['protocol_level']);
        $connectFlags = 0;
        if (!empty($array['clean_session'])) {
            $connectFlags |= 1 << 1;
        }
        if (!empty($array['will'])) {
            $connectFlags |= 1 << 2;
            $connectFlags |= $array['will']['qos'] << 3;
            if ($array['will']['retain']) {
                $connectFlags |= 1 << 5;
            }
        }
        if (!empty($array['password'])) {
            $connectFlags |= 1 << 6;
        }
        if (!empty($array['user_name'])) {
            $connectFlags |= 1 << 7;
        }
        $body .= chr($connectFlags);

        $keepAlive = !empty($array['keep_alive']) && (int) $array['keep_alive'] >= 0 ? (int) $array['keep_alive'] : 0;
        $body .= PackTool::shortInt($keepAlive);

        $propertiesTotalLength = 0;
        if (!empty($array['properties']['session_expiry_interval'])) {
            $propertiesTotalLength += 5;
        }
        if (!empty($array['properties']['receive_maximum'])) {
            $propertiesTotalLength += 3;
        }
        if (!empty($array['properties']['topic_alias_maximum'])) {
            $propertiesTotalLength += 3;
        }
        $body .= chr($propertiesTotalLength);

        if (!empty($array['properties']['session_expiry_interval'])) {
            $body .= chr(Property::SESSION_EXPIRY_INTERVAL);
            $body .= PackTool::longInt($array['properties']['session_expiry_interval']);
        }
        if (!empty($array['properties']['receive_maximum'])) {
            $body .= chr(Property::RECEIVE_MAXIMUM);
            $body .= PackTool::shortInt($array['properties']['receive_maximum']);
        }
        if (!empty($array['properties']['topic_alias_maximum'])) {
            $body .= chr(Property::TOPIC_ALIAS_MAXIMUM);
            $body .= PackTool::shortInt($array['properties']['topic_alias_maximum']);
        }

        $body .= PackTool::string($array['client_id']);
        if (!empty($array['will'])) {
            $willPropertiesTotalLength = 0;
            if (!empty($array['will']['properties']['will_delay_interval'])) {
                $willPropertiesTotalLength += 5;
            }
            if (!empty($array['will']['properties']['message_expiry_interval'])) {
                $willPropertiesTotalLength += 5;
            }
            if (!empty($array['will']['properties']['content_type'])) {
                $willPropertiesTotalLength += 3;
                $willPropertiesTotalLength += strlen($array['will']['properties']['content_type']);
            }
            if (isset($array['will']['properties']['payload_format_indicator'])) {
                $willPropertiesTotalLength += 2;
            }
            $body .= chr($willPropertiesTotalLength);

            if (!empty($array['will']['properties']['will_delay_interval'])) {
                $body .= chr(Property::WILL_DELAY_INTERVAL);
                $body .= PackTool::longInt($array['will']['properties']['will_delay_interval']);
            }
            if (!empty($array['will']['properties']['message_expiry_interval'])) {
                $body .= chr(Property::MESSAGE_EXPIRY_INTERVAL);
                $body .= PackTool::longInt($array['will']['properties']['message_expiry_interval']);
            }
            if (!empty($array['will']['properties']['content_type'])) {
                $body .= chr(Property::CONTENT_TYPE);
                $body .= PackTool::string($array['will']['properties']['content_type']);
            }
            if (isset($array['will']['properties']['payload_format_indicator'])) {
                $body .= chr(Property::PAYLOAD_FORMAT_INDICATOR);
                $body .= chr((int) $array['will']['properties']['payload_format_indicator']);
            }

            $body .= PackTool::string($array['will']['topic']);
            $body .= PackTool::string($array['will']['message']);
        }
        if (!empty($array['user_name'])) {
            $body .= PackTool::string($array['user_name']);
        }
        if (!empty($array['password'])) {
            $body .= PackTool::string($array['password']);
        }
        $head = PackTool::packHeader(Types::CONNECT, strlen($body));

        return $head . $body;
    }

    public static function connAck(array $array): string
    {
        $body = !empty($array['session_present']) ? chr(1) : chr(0);
        $code = !empty($array['code']) ? $array['code'] : 0;
        $body .= chr($code);

        $propertiesTotalLength = 0;
        if (!empty($array['properties']['maximum_packet_size'])) {
            $propertiesTotalLength += 5;
        }
        if (!isset($array['properties']['retain_available']) || !empty($array['properties']['retain_available'])) {
            $propertiesTotalLength += 2;
        }
        if (!isset($array['properties']['shared_subscription_available']) || !empty($array['properties']['shared_subscription_available'])) {
            $propertiesTotalLength += 2;
        }
        if (!isset($array['properties']['subscription_identifier_available']) || !empty($array['properties']['subscription_identifier_available'])) {
            $propertiesTotalLength += 2;
        }
        if (isset($array['properties']['topic_alias_maximum'])) {
            $propertiesTotalLength += 3;
        }
        if (!isset($array['properties']['wildcard_subscription_available']) || !empty($array['properties']['wildcard_subscription_available'])) {
            $propertiesTotalLength += 2;
        }
        $body .= chr($propertiesTotalLength);

        if (!empty($array['properties']['maximum_packet_size'])) {
            $body .= chr(Property::MAXIMUM_PACKET_SIZE);
            $body .= PackTool::longInt($array['properties']['maximum_packet_size']);
        }

        $retainAvailable = 0;
        if (!isset($array['properties']['retain_available']) || !empty($array['properties']['retain_available'])) {
            $retainAvailable = 1;
        }
        $body .= chr(Property::RETAIN_AVAILABLE);
        $body .= chr($retainAvailable);

        $sharedSubscriptionAvailable = 0;
        if (!isset($array['properties']['shared_subscription_available']) || !empty($array['properties']['shared_subscription_available'])) {
            $sharedSubscriptionAvailable = 1;
        }
        $body .= chr(Property::SHARED_SUBSCRIPTION_AVAILABLE);
        $body .= chr($sharedSubscriptionAvailable);

        $subscriptionIdentifierAvailable = 0;
        if (!isset($array['properties']['subscription_identifier_available']) || !empty($array['properties']['subscription_identifier_available'])) {
            $subscriptionIdentifierAvailable = 1;
        }
        $body .= chr(Property::SUBSCRIPTION_IDENTIFIER_AVAILABLE);
        $body .= chr($subscriptionIdentifierAvailable);

        $topicAliasMaximum = 0;
        if (isset($array['properties']['topic_alias_maximum'])) {
            $topicAliasMaximum = $array['properties']['topic_alias_maximum'];
        }
        $body .= chr(Property::TOPIC_ALIAS_MAXIMUM);
        $body .= PackTool::shortInt($topicAliasMaximum);

        $wildcardSubscriptionAvailable = 0;
        if (!isset($array['properties']['wildcard_subscription_available']) || !empty($array['properties']['wildcard_subscription_available'])) {
            $wildcardSubscriptionAvailable = 1;
        }
        $body .= chr(Property::WILDCARD_SUBSCRIPTION_AVAILABLE);
        $body .= chr($wildcardSubscriptionAvailable);

        $head = PackTool::packHeader(Types::CONNACK, strlen($body));

        return $head . $body;
    }

    public static function publish(array $array): string
    {
        $body = PackTool::string($array['topic']);
        $qos = $array['qos'] ?? 0;
        if ($qos) {
            $body .= PackTool::shortInt($array['message_id']);
        }

        $propertiesTotalLength = 0;
        if (!empty($array['properties']['message_expiry_interval'])) {
            $propertiesTotalLength += 5;
        }
        if (!empty($array['properties']['topic_alias'])) {
            $propertiesTotalLength += 3;
        }
        $body .= chr($propertiesTotalLength);

        if (!empty($array['properties']['message_expiry_interval'])) {
            $body .= chr(Property::MESSAGE_EXPIRY_INTERVAL);
            $body .= PackTool::longInt($array['properties']['message_expiry_interval']);
        }
        if (!empty($array['properties']['topic_alias'])) {
            $body .= chr(Property::TOPIC_ALIAS);
            $body .= PackTool::shortInt($array['properties']['topic_alias']);
        }

        $body .= $array['message'];
        $dup = $array['dup'] ?? 0;
        $retain = $array['retain'] ?? 0;
        $head = PackTool::packHeader(Types::PUBLISH, strlen($body), $dup, $qos, $retain);

        return $head . $body;
    }

    public static function subscribe(array $array): string
    {
        $body = PackTool::shortInt($array['message_id']);

        $propertiesTotalLength = 0;
        $body .= chr($propertiesTotalLength);

        foreach ($array['topics'] as $topic => $options) {
            $body .= PackTool::string($topic);

            $subscribeOptions = 0;
            if (isset($options['qos'])) {
                $subscribeOptions |= (int) $options['qos'];
            }
            if (isset($options['no_local'])) {
                $subscribeOptions |= (int) $options['no_local'] << 2;
            }
            if (isset($options['retain_as_published'])) {
                $subscribeOptions |= (int) $options['retain_as_published'] << 3;
            }
            if (isset($options['retain_handling'])) {
                $subscribeOptions |= (int) $options['retain_handling'] << 4;
            }
            $body .= chr($subscribeOptions);
        }

        $head = PackTool::packHeader(Types::SUBSCRIBE, strlen($body), 0, 1);

        return $head . $body;
    }

    public static function subAck(array $array): string
    {
        $body = PackTool::shortInt($array['message_id']);
        $propertiesTotalLength = 0;
        $body .= chr($propertiesTotalLength);

        $body .= call_user_func_array(
            'pack',
            array_merge(['C*'], $array['payload'])
        );
        $head = PackTool::packHeader(Types::SUBACK, strlen($body));

        return $head . $body;
    }

    public static function unSubscribe(array $array): string
    {
        $body = PackTool::shortInt($array['message_id']);
        $propertiesTotalLength = 0;
        $body .= chr($propertiesTotalLength);

        foreach ($array['topics'] as $topic) {
            $body .= PackTool::string($topic);
        }
        $head = PackTool::packHeader(Types::UNSUBSCRIBE, strlen($body), 0, 1);

        return $head . $body;
    }

    public static function unSubAck(array $array): string
    {
        $body = PackTool::shortInt($array['message_id']);
        $propertiesTotalLength = 0;
        $body .= chr($propertiesTotalLength);

        $code = !empty($array['code']) ? $array['code'] : ReasonCode::SUCCESS;
        $body .= chr($code);
        $head = PackTool::packHeader(Types::UNSUBACK, strlen($body));

        return $head . $body;
    }

    public static function disconnect(array $array): string
    {
        $code = !empty($array['code']) ? $array['code'] : ReasonCode::NORMAL_DISCONNECTION;
        $body = chr($code);
        $head = PackTool::packHeader(Types::DISCONNECT, strlen($body));

        return $head . $body;
    }

    public static function genReasonPhrase(array $array): string
    {
        $body = PackTool::shortInt($array['message_id']);
        $code = !empty($array['code']) ? $array['code'] : ReasonCode::SUCCESS;
        $body .= chr($code);

        $propertiesTotalLength = 0;
        $body .= chr($propertiesTotalLength);

        if ($array['type'] === Types::PUBREL) {
            $head = PackTool::packHeader($array['type'], strlen($body), 0, 1);
        } else {
            $head = PackTool::packHeader($array['type'], strlen($body));
        }

        return $head . $body;
    }
}
