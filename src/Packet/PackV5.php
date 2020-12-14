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
use Simps\MQTT\Types;

class PackV5
{
    public static function connect(array $array): string
    {
        $body = static::string($array['protocol_name']) . chr($array['protocol_level']);
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
        $body .= pack('n', $keepAlive);

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
            $body .= static::longInt($array['properties']['session_expiry_interval']);
        }
        if (!empty($array['properties']['receive_maximum'])) {
            $body .= chr(Property::RECEIVE_MAXIMUM);
            $body .= static::shortInt($array['properties']['receive_maximum']);
        }
        if (!empty($array['properties']['topic_alias_maximum'])) {
            $body .= chr(Property::TOPIC_ALIAS_MAXIMUM);
            $body .= static::shortInt($array['properties']['topic_alias_maximum']);
        }

        $body .= static::string($array['client_id']);
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
                $body .= static::longInt($array['will']['properties']['will_delay_interval']);
            }
            if (!empty($array['will']['properties']['message_expiry_interval'])) {
                $body .= chr(Property::MESSAGE_EXPIRY_INTERVAL);
                $body .= static::longInt($array['will']['properties']['message_expiry_interval']);
            }
            if (!empty($array['will']['properties']['content_type'])) {
                $body .= chr(Property::CONTENT_TYPE);
                $body .= static::string($array['will']['properties']['content_type']);
            }
            if (isset($array['will']['properties']['payload_format_indicator'])) {
                $body .= chr(Property::PAYLOAD_FORMAT_INDICATOR);
                $body .= chr((int) $array['will']['properties']['payload_format_indicator']);
            }

            $body .= static::string($array['will']['topic']);
            $body .= static::string($array['will']['message']);
        }
        if (!empty($array['user_name'])) {
            $body .= static::string($array['user_name']);
        }
        if (!empty($array['password'])) {
            $body .= static::string($array['password']);
        }
        $head = static::packHeader(Types::CONNECT, strlen($body));

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
            $body .= pack('N', $array['properties']['maximum_packet_size']);
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
        $body .= pack('n', $topicAliasMaximum);

        $wildcardSubscriptionAvailable = 0;
        if (!isset($array['properties']['wildcard_subscription_available']) || !empty($array['properties']['wildcard_subscription_available'])) {
            $wildcardSubscriptionAvailable = 1;
        }
        $body .= chr(Property::WILDCARD_SUBSCRIPTION_AVAILABLE);
        $body .= chr($wildcardSubscriptionAvailable);

        $head = static::packHeader(Types::CONNACK, strlen($body));

        return $head . $body;
    }

    public static function subscribe(array $array): string
    {
        $body = pack('n', $array['message_id']);

        $propertiesTotalLength = 0;
        $body .= chr($propertiesTotalLength);

        foreach ($array['topics'] as $topic => $options) {
            $body .= static::string($topic);

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

        $head = static::packHeader(Types::SUBSCRIBE, strlen($body), 0, 1);

        return $head . $body;
    }

    public static function disconnect(array $array): string
    {
        $code = !empty($array['code']) ? $array['code'] : ReasonCode::NORMAL_DISCONNECTION;
        $body = chr($code);
        $head = static::packHeader(Types::DISCONNECT, strlen($body));

        return $head . $body;
    }

    private static function string(string $str): string
    {
        $len = strlen($str);

        return pack('n', $len) . $str;
    }

    private static function longInt($int)
    {
        return pack('N', $int);
    }

    private static function shortInt($int)
    {
        return pack('n', $int);
    }

    public static function packHeader(int $type, int $bodyLength, int $dup = 0, int $qos = 0, int $retain = 0): string
    {
        $type = $type << 4;
        if ($dup) {
            $type |= 1 << 3;
        }
        if ($qos) {
            $type |= $qos << 1;
        }
        if ($retain) {
            $type |= 1;
        }

        return chr($type) . static::packRemainingLength($bodyLength);
    }

    private static function packRemainingLength(int $bodyLength)
    {
        $string = '';
        do {
            $digit = $bodyLength % 128;
            $bodyLength = $bodyLength >> 7;
            if ($bodyLength > 0) {
                $digit = ($digit | 0x80);
            }
            $string .= chr($digit);
        } while ($bodyLength > 0);

        return $string;
    }
}
