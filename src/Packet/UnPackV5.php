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

use Simps\MQTT\Exception\LengthException;
use Simps\MQTT\Hex\Property;
use Simps\MQTT\Hex\ReasonCode;
use Simps\MQTT\Types;

class UnPackV5
{
    public static function connect(string $remaining): array
    {
        $protocolName = static::string($remaining);
        $protocolLevel = ord($remaining[0]);
        $cleanSession = ord($remaining[1]) >> 1 & 0x1;
        $willFlag = ord($remaining[1]) >> 2 & 0x1;
        $willQos = ord($remaining[1]) >> 3 & 0x3;
        $willRetain = ord($remaining[1]) >> 5 & 0x1;
        $passwordFlag = ord($remaining[1]) >> 6 & 0x1;
        $userNameFlag = ord($remaining[1]) >> 7 & 0x1;
        $remaining = substr($remaining, 2);
        $keepAlive = static::shortInt($remaining);
        $propertiesTotalLength = ord($remaining[0]);
        $remaining = substr($remaining, 1);
        if ($propertiesTotalLength) {
            $sessionExpiryIntervalFlag = ord($remaining[0]) & ~Property::SESSION_EXPIRY_INTERVAL;
            if ($sessionExpiryIntervalFlag === 0) {
                $remaining = substr($remaining, 1);
                $sessionExpiryInterval = static::longInt($remaining);
            }
            $receiveMaximumFlag = ord($remaining[0]) & ~Property::RECEIVE_MAXIMUM;
            if ($receiveMaximumFlag === 0) {
                $remaining = substr($remaining, 1);
                $receiveMaximum = static::shortInt($remaining);
            }
            $topicAliasMaximumFlag = ord($remaining[0]) & ~Property::TOPIC_ALIAS_MAXIMUM;
            if ($topicAliasMaximumFlag === 0) {
                $remaining = substr($remaining, 1);
                $topicAliasMaximum = static::shortInt($remaining);
            }
        }
        $clientId = static::string($remaining);
        if ($willFlag) {
            $willPropertiesTotalLength = ord($remaining[0]);
            $remaining = substr($remaining, 1);
            if ($willPropertiesTotalLength) {
                $willDelayIntervalFlag = ord($remaining[0]) & ~Property::WILL_DELAY_INTERVAL;
                if ($willDelayIntervalFlag === 0) {
                    $remaining = substr($remaining, 1);
                    $willDelayInterval = static::longInt($remaining);
                }
                $messageExpiryIntervalFlag = ord($remaining[0]) & ~Property::MESSAGE_EXPIRY_INTERVAL;
                if ($messageExpiryIntervalFlag === 0) {
                    $remaining = substr($remaining, 1);
                    $messageExpiryInterval = static::longInt($remaining);
                }
                $contentTypeFlag = ord($remaining[0]) & ~Property::CONTENT_TYPE;
                if ($contentTypeFlag === 0) {
                    $remaining = substr($remaining, 1);
                    $contentType = static::string($remaining);
                }
                $payloadFormatIndicatorFlag = ord($remaining[0]) & ~Property::PAYLOAD_FORMAT_INDICATOR;
                if ($payloadFormatIndicatorFlag === 0) {
                    $payloadFormatIndicator = ord($remaining[1]);
                    $remaining = substr($remaining, 2);
                }
            }
            $willTopic = static::string($remaining);
            $willMessage = static::string($remaining);
        }
        $userName = $password = '';
        if ($userNameFlag) {
            $userName = static::string($remaining);
        }
        if ($passwordFlag) {
            $password = static::string($remaining);
        }
        $package = [
            'type' => Types::CONNECT,
            'protocol_name' => $protocolName,
            'protocol_level' => $protocolLevel,
            'clean_session' => $cleanSession,
            'properties' => [],
            'will' => [],
            'user_name' => $userName,
            'password' => $password,
            'keep_alive' => $keepAlive,
        ];

        if ($propertiesTotalLength) {
            if ($sessionExpiryIntervalFlag === 0) {
                $package['properties']['session_expiry_interval'] = $sessionExpiryInterval;
            }
            if ($receiveMaximumFlag === 0) {
                $package['properties']['receive_maximum'] = $receiveMaximum;
            }
            if ($receiveMaximumFlag === 0) {
                $package['properties']['topic_alias_aximum'] = $topicAliasMaximum;
            }
        } else {
            unset($package['properties']);
        }

        $package['client_id'] = $clientId;

        if ($willFlag) {
            if ($willPropertiesTotalLength) {
                if ($willDelayIntervalFlag === 0) {
                    $package['will']['properties']['will_delay_interval'] = $willDelayInterval;
                }
                if ($messageExpiryIntervalFlag === 0) {
                    $package['will']['properties']['message_expiry_interval'] = $messageExpiryInterval;
                }
                if ($contentTypeFlag === 0) {
                    $package['will']['properties']['content_type'] = $contentType;
                }
                if ($payloadFormatIndicatorFlag === 0) {
                    $package['will']['properties']['payload_format_indicator'] = $payloadFormatIndicator;
                }
            }
            $package['will'] += [
                'qos' => $willQos,
                'retain' => $willRetain,
                'topic' => $willTopic,
                'message' => $willMessage,
            ];
        } else {
            unset($package['will']);
        }

        return $package;
    }

    public static function connAck(string $remaining): array
    {
        $sessionPresent = ord($remaining[0]) & 0x01;
        $code = ord($remaining[1]);
        $remaining = substr($remaining, 2);
        $propertiesTotalLength = ord($remaining[0]);
        $remaining = substr($remaining, 1);
        if ($propertiesTotalLength) {
            $maximumPacketSizeFlag = ord($remaining[0]) & ~Property::MAXIMUM_PACKET_SIZE;
            if ($maximumPacketSizeFlag === 0) {
                $remaining = substr($remaining, 1);
                $maximumPacketSize = static::longInt($remaining);
            }
            $retainAvailableFlag = ord($remaining[0]) & ~Property::RETAIN_AVAILABLE;
            if ($retainAvailableFlag === 0) {
                $retainAvailable = ord($remaining[1]);
                $remaining = substr($remaining, 2);
            }
            $sharedSubscriptionAvailableFlag = ord($remaining[0]) & ~Property::SHARED_SUBSCRIPTION_AVAILABLE;
            if ($sharedSubscriptionAvailableFlag === 0) {
                $sharedSubscriptionAvailable = ord($remaining[1]);
                $remaining = substr($remaining, 2);
            }
            $subscriptionIdentifierAvailableFlag = ord($remaining[0]) & ~Property::SUBSCRIPTION_IDENTIFIER_AVAILABLE;
            if ($subscriptionIdentifierAvailableFlag === 0) {
                $subscriptionIdentifierAvailable = ord($remaining[1]);
                $remaining = substr($remaining, 2);
            }
            $topicAliasMaximumFlag = ord($remaining[0]) & ~Property::TOPIC_ALIAS_MAXIMUM;
            if ($topicAliasMaximumFlag === 0) {
                $remaining = substr($remaining, 1);
                $topicAliasMaximum = static::shortInt($remaining);
            }
            $wildcardSubscriptionAvailableFlag = ord($remaining[0]) & ~Property::WILDCARD_SUBSCRIPTION_AVAILABLE;
            if ($wildcardSubscriptionAvailableFlag === 0) {
                $wildcardSubscriptionAvailable = ord($remaining[1]);
                $remaining = substr($remaining, 2);
            }
        }

        $package = [
            'type' => Types::CONNACK,
            'session_present' => $sessionPresent,
            'code' => $code,
            'properties' => [],
        ];
        if ($propertiesTotalLength) {
            if ($maximumPacketSizeFlag === 0) {
                $package['properties']['maximum_packet_size'] = $maximumPacketSize;
            }
            if ($retainAvailableFlag === 0) {
                $package['properties']['retain_available'] = $retainAvailable;
            }
            if ($sharedSubscriptionAvailableFlag === 0) {
                $package['properties']['shared_subscription_available'] = $sharedSubscriptionAvailable;
            }
            if ($subscriptionIdentifierAvailableFlag === 0) {
                $package['properties']['subscription_identifier_available'] = $subscriptionIdentifierAvailable;
            }
            if ($topicAliasMaximumFlag === 0) {
                $package['properties']['topic_alias_maximum'] = $topicAliasMaximum;
            }
            if ($wildcardSubscriptionAvailableFlag === 0) {
                $package['properties']['wildcard_subscription_available'] = $wildcardSubscriptionAvailable;
            }
        } else {
            unset($package['properties']);
        }

        return $package;
    }

    public static function publish(int $dup, int $qos, int $retain, string $remaining): array
    {
        $topic = static::string($remaining);
        if ($qos) {
            $messageId = static::shortInt($remaining);
        }

        $propertiesTotalLength = ord($remaining[0]);
        $remaining = substr($remaining, 1);
        if ($propertiesTotalLength) {
            // TODO PUBLISH Properties
            $topicAliasFlag = ord($remaining[0]) & ~Property::TOPIC_ALIAS;
            if ($topicAliasFlag === 0) {
                $remaining = substr($remaining, 1);
                $topicAlias = static::shortInt($remaining);
            }
        }

        $package = [
            'type' => Types::PUBLISH,
            'topic' => $topic,
            'message' => $remaining,
            'dup' => $dup,
            'qos' => $qos,
            'retain' => $retain,
        ];

        if ($qos) {
            $package['message_id'] = $messageId;
        }

        if ($propertiesTotalLength) {
            if ($topicAliasFlag === 0) {
                $package['properties']['topic_alias'] = $topicAlias;
            }
        }

        return $package;
    }

    public static function subscribe(string $remaining): array
    {
        $messageId = static::shortInt($remaining);
        $propertiesTotalLength = ord($remaining[0]);
        $remaining = substr($remaining, 1);
        if ($propertiesTotalLength) {
            // TODO SUBSCRIBE Properties
        }
        $topics = [];
        while ($remaining) {
            $topic = static::string($remaining);
            $topics[$topic] = [
                'qos' => ord($remaining[0]) & 0x3,
                'no_local' => (bool) (ord($remaining[0]) >> 2 & 0x1),
                'retain_as_published' => (bool) (ord($remaining[0]) >> 3 & 0x1),
                'retain_handling' => ord($remaining[0]) >> 4,
            ];
            $remaining = substr($remaining, 1);
        }

        return [
            'type' => Types::SUBSCRIBE,
            'message_id' => $messageId,
            'topics' => $topics,
        ];
    }

    public static function subAck(string $remaining): array
    {
        $messageId = static::shortInt($remaining);
        $propertiesTotalLength = ord($remaining[0]);
        $remaining = substr($remaining, 1);
        if ($propertiesTotalLength) {
            // TODO SUBACK Properties
        }

        $tmp = unpack('C*', $remaining);

        return ['type' => Types::SUBACK, 'message_id' => $messageId, 'codes' => array_values($tmp)];
    }

    public static function unSubscribe(string $remaining): array
    {
        $messageId = static::shortInt($remaining);
        $propertiesTotalLength = ord($remaining[0]);
        $remaining = substr($remaining, 1);
        if ($propertiesTotalLength) {
            // TODO UNSUBSCRIBE Properties
        }
        $topics = [];
        while ($remaining) {
            $topic = static::string($remaining);
            $topics[] = $topic;
        }

        return ['type' => Types::UNSUBSCRIBE, 'message_id' => $messageId, 'topics' => $topics];
    }

    public static function unSubAck(string $remaining): array
    {
        $messageId = static::shortInt($remaining);
        $propertiesTotalLength = ord($remaining[0]);
        $remaining = substr($remaining, 1);
        if ($propertiesTotalLength) {
            // TODO UNSUBACK Properties
        }

        if ($remaining[0]) {
            $code = ord($remaining[0]);
        } else {
            $code = ReasonCode::SUCCESS;
        }
        $msg = ReasonCode::getReasonPhrase($code);

        return [
            'type' => Types::UNSUBACK,
            'message_id' => $messageId,
            'code' => $code,
            'message' => $msg,
        ];
    }

    public static function disconnect(string $remaining): array
    {
        if ($remaining[0]) {
            $code = ord($remaining[0]);
            $msg = ReasonCode::getReasonPhrase($code);
        } else {
            $code = ReasonCode::NORMAL_DISCONNECTION;
            $msg = 'Normal disconnection';
        }

        return [
            'type' => Types::DISCONNECT,
            'code' => $code,
            'message' => $msg,
        ];
    }

    public static function getReasonCode(int $type, string $remaining): array
    {
        $messageId = static::shortInt($remaining);

        if ($remaining[0]) {
            $code = ord($remaining[0]);
        } else {
            $code = ReasonCode::SUCCESS;
        }
        $msg = ReasonCode::getReasonPhrase($code);
        $remaining = substr($remaining, 1);

        $propertiesTotalLength = ord($remaining[0]);
        $remaining = substr($remaining, 1);
        if ($propertiesTotalLength) {
            // TODO Properties
        }

        return [
            'type' => $type,
            'message_id' => $messageId,
            'code' => $code,
            'message' => $msg,
        ];
    }

    private static function string(&$remaining)
    {
        $length = unpack('n', $remaining)[1];
        if ($length + 2 > strlen($remaining)) {
            throw new LengthException("unpack remaining length error, get {$length}");
        }
        $string = substr($remaining, 2, $length);
        $remaining = substr($remaining, $length + 2);

        return $string;
    }

    private static function shortInt(&$remaining)
    {
        $tmp = unpack('n', $remaining);
        $remaining = substr($remaining, 2);

        return $tmp[1];
    }

    private static function longInt(&$remaining)
    {
        $tmp = unpack('N', $remaining);
        $remaining = substr($remaining, 4);

        return $tmp[1];
    }
}
