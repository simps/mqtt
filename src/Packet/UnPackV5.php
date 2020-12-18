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
use Simps\MQTT\Tools\UnPackTool;
use Simps\MQTT\Types;

class UnPackV5
{
    public static function connect(string $remaining): array
    {
        $protocolName = UnPackTool::string($remaining);
        $protocolLevel = ord($remaining[0]);
        $cleanSession = ord($remaining[1]) >> 1 & 0x1;
        $willFlag = ord($remaining[1]) >> 2 & 0x1;
        $willQos = ord($remaining[1]) >> 3 & 0x3;
        $willRetain = ord($remaining[1]) >> 5 & 0x1;
        $passwordFlag = ord($remaining[1]) >> 6 & 0x1;
        $userNameFlag = ord($remaining[1]) >> 7 & 0x1;
        $remaining = substr($remaining, 2);
        $keepAlive = UnPackTool::shortInt($remaining);
        $propertiesTotalLength = ord($remaining[0]);
        $remaining = substr($remaining, 1);
        if ($propertiesTotalLength) {
            $sessionExpiryIntervalFlag = ord($remaining[0]) & ~Property::SESSION_EXPIRY_INTERVAL;
            if ($sessionExpiryIntervalFlag === 0) {
                $remaining = substr($remaining, 1);
                $sessionExpiryInterval = UnPackTool::longInt($remaining);
            }
            $receiveMaximumFlag = ord($remaining[0]) & ~Property::RECEIVE_MAXIMUM;
            if ($receiveMaximumFlag === 0) {
                $remaining = substr($remaining, 1);
                $receiveMaximum = UnPackTool::shortInt($remaining);
            }
            $topicAliasMaximumFlag = ord($remaining[0]) & ~Property::TOPIC_ALIAS_MAXIMUM;
            if ($topicAliasMaximumFlag === 0) {
                $remaining = substr($remaining, 1);
                $topicAliasMaximum = UnPackTool::shortInt($remaining);
            }
        }
        $clientId = UnPackTool::string($remaining);
        if ($willFlag) {
            $willPropertiesTotalLength = ord($remaining[0]);
            $remaining = substr($remaining, 1);
            if ($willPropertiesTotalLength) {
                $willDelayIntervalFlag = ord($remaining[0]) & ~Property::WILL_DELAY_INTERVAL;
                if ($willDelayIntervalFlag === 0) {
                    $remaining = substr($remaining, 1);
                    $willDelayInterval = UnPackTool::longInt($remaining);
                }
                $messageExpiryIntervalFlag = ord($remaining[0]) & ~Property::MESSAGE_EXPIRY_INTERVAL;
                if ($messageExpiryIntervalFlag === 0) {
                    $remaining = substr($remaining, 1);
                    $messageExpiryInterval = UnPackTool::longInt($remaining);
                }
                $contentTypeFlag = ord($remaining[0]) & ~Property::CONTENT_TYPE;
                if ($contentTypeFlag === 0) {
                    $remaining = substr($remaining, 1);
                    $contentType = UnPackTool::string($remaining);
                }
                $payloadFormatIndicatorFlag = ord($remaining[0]) & ~Property::PAYLOAD_FORMAT_INDICATOR;
                if ($payloadFormatIndicatorFlag === 0) {
                    $payloadFormatIndicator = ord($remaining[1]);
                    $remaining = substr($remaining, 2);
                }
            }
            $willTopic = UnPackTool::string($remaining);
            $willMessage = UnPackTool::string($remaining);
        }
        $userName = $password = '';
        if ($userNameFlag) {
            $userName = UnPackTool::string($remaining);
        }
        if ($passwordFlag) {
            $password = UnPackTool::string($remaining);
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
                $maximumPacketSize = UnPackTool::longInt($remaining);
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
                $topicAliasMaximum = UnPackTool::shortInt($remaining);
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
        $topic = UnPackTool::string($remaining);
        if ($qos) {
            $messageId = UnPackTool::shortInt($remaining);
        }

        $propertiesTotalLength = ord($remaining[0]);
        $remaining = substr($remaining, 1);
        if ($propertiesTotalLength) {
            // TODO PUBLISH Properties
            $messageExpiryIntervalFlag = ord($remaining[0]) & ~Property::MESSAGE_EXPIRY_INTERVAL;
            if ($messageExpiryIntervalFlag === 0) {
                $remaining = substr($remaining, 1);
                $messageExpiryInterval = UnPackTool::longInt($remaining);
            }
            $topicAliasFlag = ord($remaining[0]) & ~Property::TOPIC_ALIAS;
            if ($topicAliasFlag === 0) {
                $remaining = substr($remaining, 1);
                $topicAlias = UnPackTool::shortInt($remaining);
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
            if ($messageExpiryIntervalFlag === 0) {
                $package['properties']['message_expiry_interval'] = $messageExpiryInterval;
            }
            if ($topicAliasFlag === 0) {
                $package['properties']['topic_alias'] = $topicAlias;
            }
        }

        return $package;
    }

    public static function subscribe(string $remaining): array
    {
        $messageId = UnPackTool::shortInt($remaining);
        $propertiesTotalLength = ord($remaining[0]);
        $remaining = substr($remaining, 1);
        if ($propertiesTotalLength) {
            // TODO SUBSCRIBE Properties
        }
        $topics = [];
        while ($remaining) {
            $topic = UnPackTool::string($remaining);
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
        $messageId = UnPackTool::shortInt($remaining);
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
        $messageId = UnPackTool::shortInt($remaining);
        $propertiesTotalLength = ord($remaining[0]);
        $remaining = substr($remaining, 1);
        if ($propertiesTotalLength) {
            // TODO UNSUBSCRIBE Properties
        }
        $topics = [];
        while ($remaining) {
            $topic = UnPackTool::string($remaining);
            $topics[] = $topic;
        }

        return ['type' => Types::UNSUBSCRIBE, 'message_id' => $messageId, 'topics' => $topics];
    }

    public static function unSubAck(string $remaining): array
    {
        $messageId = UnPackTool::shortInt($remaining);
        $propertiesTotalLength = ord($remaining[0]);
        $remaining = substr($remaining, 1);
        if ($propertiesTotalLength) {
            // TODO UNSUBACK Properties
        }

        if (isset($remaining[0])) {
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
        if (isset($remaining[0])) {
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
        $messageId = UnPackTool::shortInt($remaining);

        if (isset($remaining[0])) {
            $code = ord($remaining[0]);
        } else {
            $code = ReasonCode::SUCCESS;
        }
        $msg = ReasonCode::getReasonPhrase($code);
        $remaining = substr($remaining, 1);

        $propertiesTotalLength = 0;
        if (isset($remaining[0])) {
            $propertiesTotalLength = ord($remaining[0]);
            $remaining = substr($remaining, 1);
        }

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
}
